<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_tags
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * The Tags List Controller
 *
 * @since  3.1
 */
use Google\Cloud\Translate\TranslateClient;

class ToolsControllerTest extends JControllerLegacy
{
    public function google_translate()
    {
        # Includes the autoloader for libraries installed with composer
        require JPATH_ROOT . DS . 'libraries/google-cloud-php-master/vendor/autoload.php';

# Imports the Google Cloud client library

# Your Google Cloud Platform project ID
        $projectId = 'api-project-639885786208';

# Instantiates a client
        $translate = new TranslateClient([
            'projectId' => $projectId
        ]);

# The text to translate
        $text = 'Hello, world!';
# The target language
        $target = 'ru';

# Translates some text into Russian
        $translation = $translate->translate($text, [
            'target' => $target
        ]);

        echo 'Text: ' . $text . '
Translation: ' . $translation['text'];

    }
}
