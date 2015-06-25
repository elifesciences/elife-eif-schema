var fs = require('fs');
var path = require('path');
var validator = require('is-my-json-valid/require');

var options = require("nomnom")
    .options({
        file: {
            position: 0,
            help: "file to parse; otherwise uses stdin"
        }
    }).parse();

function main(args) {
    var source = '';
    if (options.file) {
        var json = path.normalize(options.file);
        source = fs.readFileSync(json, 'utf8');
        validate(source);
    } else {
        var stdin = process.openStdin();
        stdin.setEncoding('utf8');

        stdin.on('data', function (chunk) {
            source += chunk.toString('utf8');
        });
        stdin.on('end', function () {
            validate(source);
        });
    }
}

function validate(source) {
    var json = JSON.parse(source);
    var validate = validator('schema.json', {verbose: true});

    validate(json);

    if (validate.errors) {
        console.log(JSON.stringify(validate.errors));
        process.exit(1);
    } else {
        process.exit();
    }
}

main(process.argv.slice(1));
