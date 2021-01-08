#!/bin/sh

PLUGIN_SLUG="dynamic-content"
PROJECT_PATH=$(pwd)
PUBLIC_PATH="${PROJECT_PATH}/public"
BUILD_PATH="${PROJECT_PATH}/build"
DEST_PATH="$BUILD_PATH/$PLUGIN_SLUG"

if [ ! -d "$PUBLIC_PATH" ]; then
	echo "Please execute in root directory."
	exit
fi

echo "Generating build directory..."
rm -rf "$BUILD_PATH"
mkdir -p "$DEST_PATH"

echo "Syncing files..."
rsync -rL "$PROJECT_PATH/public/" "$DEST_PATH/"

cd $DEST_PATH
composer dump-autoload
rm composer.json
rm composer.lock
cd ../..

echo "Generating zip file..."
cd "$BUILD_PATH" || exit
zip -q -r "${PLUGIN_SLUG}.zip" "$PLUGIN_SLUG/"

cd "$PROJECT_PATH" || exit
mv "$BUILD_PATH/${PLUGIN_SLUG}.zip" "$PROJECT_PATH"
echo "${PLUGIN_SLUG}.zip file generated!"

echo "Cleanup build path..."
rm -rf "$BUILD_PATH"

echo "Build done!"
