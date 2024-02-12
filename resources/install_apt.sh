######################### INCLUSION LIB ##########################
BASEDIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
wget https://raw.githubusercontent.com/Mips2648/dependance.lib/master/dependance.lib -O $BASEDIR/dependance.lib &>/dev/null
PLUGIN=$(basename "$(realpath $BASEDIR/..)")
LANG_DEP=en
. ${BASEDIR}/dependance.lib
##################################################################

VENV_DIR=$BASEDIR/venv
cd $BASEDIR

pre
step 5 "Clean apt"
try apt-get clean
step 10 "Update apt"
try apt-get update

step 20 "Install apt packages"
tryOrStop apt-get install -y python3 python3-pip python3-dev python3-venv

step 50 "Creating python 3 virtual environment"
tryOrStop python3 -m venv $VENV_DIR

step 60 "Setting up virtual environment"
tryOrStop $VENV_DIR/bin/python3 -m pip install --no-cache-dir --upgrade pip wheel

step 70 "Install the required python packages"
tryOrStop $VENV_DIR/bin/python3 -m pip install --no-cache-dir -r requirements.txt

step 90 "Summary of installed packages"
$VENV_DIR/bin/python3 -m pip freeze

post
