# !/bin/bash
if test -d $1/.git; then
    echo "Framway déjà existant, mise à jour en cours".
    cd .$1
    # rm package.json
    # rm package-lock.json
    git stash
    git pull --rebase -vvv
    git checkout package.json
else
	git clone https://github.com/SeptimusVII/framway.git $1
fi