// Compile legacy stylesheets.
import fs from 'fs';
import path from 'path';
import glob from 'glob';
import sass from 'node-sass';

function compile(source, output) {
  sass.render({
    file: source,
    outFile: output,
  }, function(error, result) {
    // No errors during the compilation, write this result on the disk
    fs.writeFile(output, result.css, function(err){
      if(!err){
        //file written on disk
        console.log(`Compiled ${output}`)
      }
    });
  });
}

const files = glob.sync(path.join(__dirname, 'src/scss/legacy/*.scss'));

for (const file of files) {
  const fileName = path.basename(file);

  compile(
    file,
    path.join(__dirname, '..', 'html/templates/shared', fileName.replace('scss', 'css'))
  );
}
