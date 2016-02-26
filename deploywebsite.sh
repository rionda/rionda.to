#! /bin/sh

UPLOADHOST=rionda.to
UPLOADDIR=rionda.to

rsync --rsh=ssh --verbose --compress --recursive --copy-links \
    --perms --no-o --no-g --chmod=F664 --chmod=D2775 \
    --exclude old --exclude cv --exclude='.git**' \
    --exclude deploywebsite.sh --exclude update_submodules.sh \
    . ${UPLOADHOST}:${UPLOADDIR}
