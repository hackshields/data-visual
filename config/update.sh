#!/bin/bash
# 
# dbface-update.sh (version:0.1)
#
# https://www.dbface.com
#
# update & partial dbface backup
# 1. create folder user/backup
# 2. zip application/, plugins/, system/ to user/backup/backup_date.zip
# 3. download zip from download link and save file to root folder
# 4. overwrite application, plugins, system folders with the files in the zip

NOW=$(date +"%Y%m%d%H%M%s")

# The directory for saving the download file and backup files
BACKUP_DIR="../user/backup"
# The dbface installation Directory, use absolute URL
DBFACE_INSTALLATION_DIR = ".."

PHPVersion=$(php -v | grep -Eo "PHP [[:digit:]]+.[[:digit:]]+" | tail -c -4);
echo "Check PHP Version: ${PHPVersion}";

DOWNLOAD_URL="https://s3-ap-southeast-1.amazonaws.com/download-dbface/v8/dbface_php${PHPVersion}.zip"
echo "Download the latest DbFace product file from ${DOWNLOAD_URL}";

echo "Prepare the backup directory"
if [ ! -d "${BACKUP_DIR}" ]; then
  mkdir ${BACKUP_DIR}
fi

mkdir ${BACKUP_DIR}/${NOW}

# copy current version to local directory
echo "Backup current installation to local directory"
mkdir ${BACKUP_DIR}/${NOW}/local
cp -R ${DBFACE_INSTALLATION_DIR}/application ${BACKUP_DIR}/${NOW}/local/
cp -R ${DBFACE_INSTALLATION_DIR}/system ${BACKUP_DIR}/${NOW}/local/
cp -R ${DBFACE_INSTALLATION_DIR}/config ${BACKUP_DIR}/${NOW}/local/
cp -R ${DBFACE_INSTALLATION_DIR}/plugins ${BACKUP_DIR}/${NOW}/local/
cp -R ${DBFACE_INSTALLATION_DIR}/static ${BACKUP_DIR}/${NOW}/local/
cp ${DBFACE_INSTALLATION_DIR}/index.php ${BACKUP_DIR}/${NOW}/local/

echo "Start download DbFace product file";
curl ${DOWNLOAD_URL} -o ${BACKUP_DIR}/${NOW}/dbface.zip

echo "Unzip product file into ${BACKUP_DIR}/${NOW}/remote";
unzip ${BACKUP_DIR}/${NOW}/dbface.zip -x "user*" -q -d ${BACKUP_DIR}/${NOW}/remote

echo "copy back all remote updated files to installation directory"
cp -ar ${BACKUP_DIR}/${NOW}/remote/application/. ${DBFACE_INSTALLATION_DIR}/application
cp -ar ${BACKUP_DIR}/${NOW}/remote/system/. ${DBFACE_INSTALLATION_DIR}/system
cp -ar ${BACKUP_DIR}/${NOW}/remote/config/. ${DBFACE_INSTALLATION_DIR}/config
cp -ar ${BACKUP_DIR}/${NOW}/remote/plugins/. ${DBFACE_INSTALLATION_DIR}/plugins
cp -ar ${BACKUP_DIR}/${NOW}/remote/static/. ${DBFACE_INSTALLATION_DIR}/static
cp ${BACKUP_DIR}/${NOW}/remote/index.php ${DBFACE_INSTALLATION_DIR}/

echo "done"

#== EOF == 
