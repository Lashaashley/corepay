const fs = require('fs');
const path = require('path');

function generateSourceMaps(directory) {
    const files = fs.readdirSync(directory);
    
    files.forEach(file => {
        const fullPath = path.join(directory, file);
        
        if (fs.statSync(fullPath).isDirectory()) {
            generateSourceMaps(fullPath);
        } else if (file.endsWith('.min.js')) {
            const mapPath = fullPath + '.map';
            
            if (!fs.existsSync(mapPath)) {
                console.log(`Generating source map for ${file}`);
                // You might need a more sophisticated source map generation method
                fs.writeFileSync(mapPath, JSON.stringify({
                    version: 3,
                    sources: [file],
                    names: [],
                    mappings: ''
                }));
            }
        }
    });
}

// Run for your resources directory
generateSourceMaps(path.join(__dirname, 'resources'));