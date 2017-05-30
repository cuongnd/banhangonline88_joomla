<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */
class JFBConnectStream
{
    var $posts = array();
    var $options;
    var $themeDir;

    var $currentRow;

    public function __construct(JRegistry $options, $channels = null)
    {
        $this->options = $options;

        JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_jfbconnect/tables/');

        if ($channels)
        {
            foreach ($channels as $channel)
            {
                if ($channel)
                    $this->addChannel($channel);
            }
            $this->orderPosts();
        }

        $this->addTheme();
    }

    /*
     * Expects an ID of valid channel. Retrieves feed item from channel and adds it to stream
     */
    public function addChannel($channel)
    {
        $channelId = trim($channel);
        $row = JTable::getInstance('Channel', 'Table');
        $row->load($channelId);
        if ($row->published)
        {
            $options = new JRegistry();
            $options->loadObject($row->attribs);
            $channel = JFBCFactory::provider($row->provider)->channel($row->type, $options);

            // This is a bit hacky as we're temporarily saving the row to use in the addPost. Should be handling this better.
            $this->currentRow = $row;
            $channel->getStream($this);
        }
    }


    public function addPost($post)
    {
        $post->channelProvider = $this->currentRow->provider;
        $post->channelType = $this->currentRow->type;

        //This timestamp format is not displayed, but needs enough information to be sorted correctly later
        $this->posts[$post->getTimestamp($post->updatedTime, 'Y-m-d H:i:s')] = $post;
    }

    public function orderPosts()
    {
        return krsort($this->posts);
    }

    private function addTheme()
    {
        $paths = array();
        $paths[] = JPATH_ROOT . '/templates/' . JFactory::getApplication()->getTemplate() . '/html/com_jfbconnect/themes/scsocialstream/default/';
        $paths[] = JPATH_ROOT . '/media/sourcecoast/themes/scsocialstream/default/';
        $theme = 'styles.css';
        $file = JPath::find($paths, $theme);
        $this->themeDir = str_replace("styles.css", "", $file);
        $file = str_replace(JPATH_SITE, '', $file);
        $file = str_replace('\\', "/", $file); //Windows support for file separators
        $document = JFactory::getDocument();
        $document->addStyleSheet(JURI::base(true) . $file);
    }

    public function render()
    {
        // Limit the posts
        $posts = array_slice($this->posts, 0, $this->options->get('post_limit', 50));

        foreach ($posts as $post)
        {
            echo $post->getHtml($this);
        }
    }
}

class JFBConnectPost
{
    var $channelProvider;
    var $channelType;

    private $settings;

    public function __construct()
    {
        $this->settings = new JRegistry();
    }

    public function __set($name, $value)
    {
        $this->settings->set($name, $value);
    }

    public function __get($name)
    {
        return $this->settings->get($name);
    }

    public function getTimestamp($timeToFormat, $dateTimeFormat)
    {
        $updatedTime = JFactory::getDate($timeToFormat);
        return $updatedTime->format($dateTimeFormat);
    }

    public function getHtml($stream)
    {
        $showLink = $stream->options->get('show_link');
        $dateTimeFormat = $stream->options->get('datetime_format');
        $date = $this->getTimestamp($this->updatedTime, $dateTimeFormat);

        $hasPageInfo = $this->thumbTitle != '' || $this->thumbCaption != '' || $this->thumbDescription != '';
        $hasPicture = $this->thumbPicture != '';

        // $stream is used by the layout files
        $layoutDir = $stream->themeDir;
        $file = strtolower($this->channelProvider . '_' . $this->channelType . '.php');
        if (!JFile::exists($layoutDir . $file))
            $file = strtolower($this->channelProvider . '.php');
        ob_start();
        require($layoutDir . $file);
        $html = ob_get_clean();
        return $html;
    }

    protected function makeClickableLinks($text)
    {
        // The funky R's are for unicode URLs
        return preg_replace('!(((f|ht)tp(s)?://)[-a-zA-Zа-яА-Я()0-9@:%_+.~#?&;//=]+)!i', '<a href="$1" rel="nofollow">$1</a>', $text);
    }
}
