#!/usr/bin/env python3
import os
import glob
import json
import fontforge # sudo apt-get install python3-fontforge

def do(fn): # Do fn or return None
    try: return fn()
    except: return None

def getAppVersion(): # Get the most recent git tag as the version.
    try: return os.popen('git describe --tags --abbrev=0').read()[1:]
    except: return None

def separator(svgFileName):
    svgBaseName = os.path.splitext(os.path.basename(svgFileName))[0].split('-')
    return { 'code' : int(svgBaseName[0], 16),
             'name' : ' '.join(svgBaseName[1:]) }

def loadConfigFile():
    try: return json.loads(open('../manager.conf', 'r').read())
    except:
        try: return json.loads(open('sample.conf', 'r').read())
        except: return {}

if __name__ == '__main__':
    # Load config data.
    config = loadConfigFile() or {}

    # Lots of try or?
    fontRoot = do(lambda: config["font-root"]) or "../"
    fontName = do(lambda: config["font-name"]) or "icon-font"
    svgPath  = do(lambda: config["svg-path"])  or "../svg"
    defaultSvgPath = do(lambda: config["default-svg"]) or "../svg"

    userSvgFiles = glob.glob(svgPath + '/*.svg')
    defaultSvgFiles = glob.glob(defaultSvgPath + '/*.svg')

    svgFiles = defaultSvgFiles + userSvgFiles
    sfdFilePath = os.path.join(fontRoot, fontName) + ".sfd"

    # Check if sfd file exist.
    if os.path.exists(sfdFilePath):
        sfdFont = fontforge.open(sfdFilePath) # open sfd font file.
    else:
        sfdFont = fontforge.font() # create a file
        sfdFont.fontname = fontName
        sfdFont.save(sfdFilePath)


    # If git was available, set the font version.
    sfdFont.version = getAppVersion() or sfdFont.version

    # Clear all old data
    sfdFont.selection.all()
    sfdFont.clear()

    sfdFont.ascent = 20
    sfdFont.descent = 4
    # Start glyphs insertion
    for file in svgFiles:
        fileInfo = separator(file) # Extract data from the filename
        glyph = sfdFont.createChar(fileInfo['code'], fileInfo['name']) # Create glyph.
        glyph.importOutlines(file, scale=False) # Import svg outline.
        glyph.width = 24
        glyph.round(2)
        print('a.{0}-{1}'.format(hex(fileInfo['code'])[2:], fileInfo['name']))

    # End glyphs update
    sfdFont.familyname = fontName
    sfdFont.save() # Save sfd file.

    baseName = (sfdFont.default_base_filename or fontName) + '.ttf'
    sfdFont.generate(os.path.join(fontRoot, baseName)) # Generate '.ttf' font.

    # Remove the generated font files.
    # os.remove(baseName)
