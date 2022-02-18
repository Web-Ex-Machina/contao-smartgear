# !/bin/bash
cd $1
sed -i "s~\"@fortawesome/fontawesome-free\": \"^5.15.4\",~~g" package.json
sed -i "s~\"@fortawesome/fontawesome-pro\": \"^5.15.4\",~~g" package.json
sed -i "s~\"@fortawesome/free-brands-svg-icons\": \"^5.15.4\",~~g" package.json
sed -i "s~\"@fortawesome/free-regular-svg-icons\": \"^5.15.4\",~~g" package.json
sed -i "s~\"@fortawesome/free-solid-svg-icons\": \"^5.15.4\",~~g" package.json
npm install --force