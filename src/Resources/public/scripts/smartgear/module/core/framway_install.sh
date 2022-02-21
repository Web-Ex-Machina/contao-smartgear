# !/bin/bash
cd $1
echo "Copie du fichier package.json"
cp package.json package_original.json
echo "Suppression des packets \"fortawesome\" du package.json"
sed -i "s~\"@fortawesome/fontawesome-free\": \"^5.15.4\",~~g" package.json
sed -i "s~\"@fortawesome/fontawesome-pro\": \"^5.15.4\",~~g" package.json
sed -i "s~\"@fortawesome/free-brands-svg-icons\": \"^5.15.4\",~~g" package.json
sed -i "s~\"@fortawesome/free-regular-svg-icons\": \"^5.15.4\",~~g" package.json
sed -i "s~\"@fortawesome/free-solid-svg-icons\": \"^5.15.4\",~~g" package.json
echo "Installation sans paquets \"fortawesome\""
npm install --force
echo "Suppression du fichier package.json"
rm package.json
echo "Restauration du fichier package.json original"
mv package_original.json package.json
echo "Installation avec paquets \"fortawesome\""
npm install --force