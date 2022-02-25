# !/bin/bash
if test -d $1/.git; then
    echo "Framway déjà existant, mise à jour en cours".
    cd .$1
    git stash
    git pull --rebase
else
	git clone https://github.com/SeptimusVII/framway.git $1
fi