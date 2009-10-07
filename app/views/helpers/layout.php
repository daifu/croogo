<?php
/**
 * Layout Helper
 *
 * PHP version 5
 *
 * @category Helper
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class LayoutHelper extends AppHelper {
/**
 * Other helpers used by this helper
 *
 * @var array
 * @access public
 */
    var $helpers = array('Html', 'Form', 'Session', 'Javascript');
/**
 * Constructor
 *
 * @param array $options options
 * @access public
 */
    function __construct($options = array()) {
        $this->View =& ClassRegistry::getObject('view');
        return parent::__construct($options);
    }
/**
 * Javascript variables
 *
 * @return string
 */
    function jsVars() {
        $output = '';

        $output .= $this->Javascript->codeBlock("var baseUrl = '" . Router::url('/') . "';");
        $params = array(
            'controller' => $this->params['controller'],
            'action' => $this->params['action'],
            'named' => $this->params['named'],
        );
        $output .= $this->Javascript->codeBlock("var params = " . $this->Javascript->object($params) . ";");

        echo $output;
    }
/**
 * Status
 *
 * instead of 0/1, show tick/cross
 *
 * @param integer $value 0 or 1
 * @return string formatted img tag
 */
    function status($value) {
        if ($value == 1) {
            $output = $this->Html->image('/img/icons/tick.png');
        } else {
            $output = $this->Html->image('/img/icons/cross.png');
        }
        return $output;
    }
/**
 * Show flash message
 *
 * @return void
 */
    function sessionFlash() {
        $messages = $this->Session->read('Message');
        if( is_array($messages) ) {
            foreach(array_keys($messages) AS $key) {
                $this->Session->flash($key);
            }
        }
    }
/**
 * Meta tags
 *
 * @return string
 */
    function meta($metaForLayout = array()) {
        $_metaForLayout = array();
        if (is_array(Configure::read('Meta'))) {
            $_metaForLayout = Configure::read('Meta');
        }

        if (count($metaForLayout) == 0 &&
            isset($this->View->viewVars['node']['CustomFields']) &&
            count($this->View->viewVars['node']['CustomFields']) > 0) {
            $metaForLayout = array();
            foreach ($this->View->viewVars['node']['CustomFields'] AS $key => $value) {
                if (strstr($key, 'meta_')) {
                    $key = str_replace('meta_', '', $key);
                    $metaForLayout[$key] = $value;
                }
            }
        }

        $metaForLayout = array_merge($_metaForLayout, $metaForLayout);

        $output = '';
        foreach ($metaForLayout AS $name => $content) {
            $output .= '<meta name="' . $name . '" content="' . $content . '" />';
        }

        return $output;
    }
/**
 * isLoggedIn
 *
 * if User is logged in
 *
 * @return boolean
 */
    function isLoggedIn() {
        if ($this->Session->check('Auth.User.id')) {
            return true;
        } else {
            return false;
        }
    }
/**
 * Feed
 *
 * RSS feeds
 *
 * @param boolean $returnUrl if true, only the URL will be returned
 * @return string
 */
    function feed($returnUrl = false) {
        if (Configure::read('Site.feed_url')) {
            $url = COnfigure::read('Site.feed_url');
        } else {
            /*$url = Router::url(array(
                'controller' => 'nodes',
                'action' => 'index',
                'type' => 'blog',
                'ext' => 'rss',
            ));*/
            $url = '/nodes/promoted.rss';
        }

        if ($returnUrl) {
            $output = $url;
        } else {
            $url = Router::url($url);
            $output = '<link href="' . $url . '" type="application/rss+xml" rel="alternate" title="RSS 2.0" />';
            return $output;
        }

        return $output;
    }
/**
 * Get Role ID
 *
 * @return integer
 */
    function getRoleId() {
        if ($this->isLoggedIn()) {
            $roleId = $this->Session->read('Auth.User.role_id');
        } else {
            // Public
            $roleId = 3;
        }
        return $roleId;
    }
/**
 * Region is empty
 *
 * returns true if Region has no Blocks.
 *
 * @param string $regionAlias Region alias
 * @return boolean
 */
    function regionIsEmpty($regionAlias) {
        if (isset($this->View->viewVars['blocks_for_layout'][$regionAlias]) &&
            count($this->View->viewVars['blocks_for_layout'][$regionAlias]) > 0) {
            return false;
        } else {
            return true;
        }
    }
/**
 * Show Blocks for a particular Region
 *
 * @param string $regionAlias Region alias
 * @param array $findOptions (optional)
 * @return string
 */
    function blocks($regionAlias, $options = array()) {
        $_options = array();
        $options = array_merge($_options, $options);

        $output = '';
        if (!$this->regionIsEmpty($regionAlias)) {
            $blocks = $this->View->viewVars['blocks_for_layout'][$regionAlias];
            foreach ($blocks AS $block) {
                if ($block['Block']['element'] != null) {
                    $element = $block['Block']['file'];
                } else {
                    $element = 'block';
                }
                $output .= $this->View->element($element, array('block' => $block));
            }
        }

        return $output;
    }
/**
 * Show Menu by Alias
 *
 * @param string $menuAlias Menu alias
 * @param array $options (optional)
 * @return string
 */
    function menu($menuAlias, $options = array()) {
        $_options = array(
            'findOptions' => array(),
            'tag' => 'ul',
            'tagAttributes' => array(),
            'containerTag' => 'div',
            'containerTagAttr' => array(
                'class' => 'menu',
            ),
            'selected' => 'selected',
            'dropdown' => false,
            'dropdownClass' => 'sf-menu',
        );
        $options = array_merge($_options, $options);

        if (!isset($this->View->viewVars['menus_for_layout'][$menuAlias])) {
            return false;
        }

        $menu = $this->View->viewVars['menus_for_layout'][$menuAlias];

        $options['containerTagAttr']['id'] = 'menu-' . $this->View->viewVars['menus_for_layout'][$menuAlias]['Menu']['id'];
        $options['containerTagAttr']['class'] .= ' menu';

        $links = $this->View->viewVars['menus_for_layout'][$menuAlias]['threaded'];
        $linksList = $this->nestedLinks($links, $options);
        $output = $this->Html->tag($options['containerTag'], $linksList, $options['containerTagAttr']);
    
        return $output;
    }
/**
 * Nested Links
 *
 * @param array $links model output (threaded)
 * @param array $options (optional)
 * @param integer $depth depth level
 * @return string
 */
    function nestedLinks($links, $options = array(), $depth = 1) {
        $_options = array();
        $options = array_merge($_options, $options);
        
        $output = '';
        foreach ($links AS $link) {
            $linkAttr = array(
                'id' => 'link-' . $link['Link']['id'],
                'rel' => $link['Link']['rel'],
                'target' => $link['Link']['target'],
                'title' => $link['Link']['description'],
            );

            // if link is in the format: controller:contacts/action:view
            if (strstr($link['Link']['link'], 'controller:')) {
                $link['Link']['link'] = $this->linkStringToArray($link['Link']['link']);
            }

            if (Router::url($link['Link']['link']) == Router::url('/' . $this->params['url']['url'])) {
                $linkAttr['class'] = $options['selected'];
            }

            $linkOutput = $this->Html->link($link['Link']['title'], $link['Link']['link'], $linkAttr);
            if (isset($link['children']) && count($link['children']) > 0) {
                $linkOutput .= $this->nestedLinks($link['children'], $options, $depth + 1);
            }
            $linkOutput = $this->Html->tag('li', $linkOutput);
            $output .= $linkOutput;
        }
        if ($output != null) {
            $tagAttr = array();
            if ($options['dropdown'] && $depth == 1) {
                $tagAttr['class'] = $options['dropdownClass'];
            }
            $output = $this->Html->tag($options['tag'], $output, $tagAttr);
        }

        return $output;
    }
/**
 * Converts strings like controller:abc/action:xyz/ to arrays
 *
 * @param string $link link
 * @return array
 */
    function linkStringToArray($link) {
        $link = explode('/', $link);
        $linkArr = array();
        foreach ($link AS $linkElement) {
            if ($linkElement != null) {
                $linkElementE = explode(':', $linkElement);
                if (isset($linkElementE['1'])) {
                    $linkArr[$linkElementE['0']] = $linkElementE['1'];
                } else {
                    $linkArr[] = $linkElement;
                }
            }
        }

        return $linkArr;
    }
/**
 * Show Vocabulary by Alias
 *
 * @param string $vocabularyAlias Vocabulary alias
 * @param array $options (optional)
 * @return string
 */
    function vocabulary($vocabularyAlias, $options = array()) {
        $_options = array(
            'tag' => 'ul',
            'tagAttr' => array(),
            'containerTag' => 'div',
            'containerTagAttr' => array(
                'class' => 'vocabulary',
            ),
            'type' => null,
            'link' => true,
        );
        $options = array_merge($_options, $options);

        $output = '';
        if (isset($this->View->viewVars['vocabularies_for_layout'][$vocabularyAlias]['list'])) {
            $vocabulary = $this->View->viewVars['vocabularies_for_layout'][$vocabularyAlias];
            foreach ($vocabulary['list'] AS $termSlug => $termTitle) {
                if ($options['link']) {
                    $li = '<li>' . $this->Html->link($termTitle, array(
                        'controller' => 'nodes',
                        'action' => 'term',
                        'type' => $options['type'],
                        'slug' => $termSlug,
                    )) . '</li>';
                } else {
                    $li = '<li>' . $termTitle . '</li>';
                }
                $output .= $li;
            }
            if ($output != '') {
                $options['containerTagAttr']['id'] = 'vocabulary-' . $vocabulary['Vocabulary']['id'];
                $output = $this->Html->tag($options['tag'], $output, $options['tagAttr']);
                $output = $this->Html->tag($options['containerTag'], $output, $options['containerTagAttr']);
            }
        }

        return $output;
    }
/**
 * Filter content
 *
 * Replaces bbcode-like element tags
 *
 * @param string $content content
 * @return string
 */
    function filter($content) {
        $content = $this->filterElements($content);
        $content = $this->filterMenus($content);
        $content = $this->filterVocabularies($content);
        return $content;
    }
/**
 * Filter content for elements
 *
 * Original post by Stefan Zollinger: http://bakery.cakephp.org/articles/view/element-helper
 * [element:element_name] or [e:element_name]
 *
 * @param string $content
 * @return string
 */
    function filterElements($content) {
        preg_match_all('/\[(element|e):([A-Za-z0-9_\-]*)(.*?)\]/i', $content, $tagMatches);
        for($i=0; $i < count($tagMatches[1]); $i++){
            $regex = '/(\S+)=[\'"]?((?:.(?![\'"]?\s+(?:\S+)=|[>\'"]))+.)[\'"]?/i';
            preg_match_all($regex, $tagMatches[3][$i], $attributes);
            $element = $tagMatches[2][$i];
            $options = array();
            for($j=0; $j < count($attributes[0]); $j++){
                $options[$attributes[1][$j]] = $attributes[2][$j];
            }
            $content = str_replace($tagMatches[0][$i], $this->View->element($element,$options), $content);
        }
        return $content;
    }
/**
 * Filter content for Menus
 *
 * Replaces [menu:menu_alias] or [m:menu_alias] with Menu list
 *
 * @param string $content
 * @return string
 */
    function filterMenus($content) {
        preg_match_all('/\[(menu|m):([A-Za-z0-9_\-]*)(.*?)\]/i', $content, $tagMatches);
        for($i=0; $i < count($tagMatches[1]); $i++){
            $regex = '/(\S+)=[\'"]?((?:.(?![\'"]?\s+(?:\S+)=|[>\'"]))+.)[\'"]?/i';
            preg_match_all($regex, $tagMatches[3][$i], $attributes);
            $menuAlias = $tagMatches[2][$i];
            $options = array();
            for($j=0; $j < count($attributes[0]); $j++){
                $options[$attributes[1][$j]] = $attributes[2][$j];
            }
            $content = str_replace($tagMatches[0][$i], $this->menu($menuAlias,$options), $content);
        }
        return $content;
    }
/**
 * Filter content for Vocabularies
 *
 * Replaces [vocabulary:vocabulary_alias] or [v:vocabulary_alias] with Terms list
 *
 * @param string $content
 * @return string
 */
    function filterVocabularies($content) {
        preg_match_all('/\[(vocabulary|v):([A-Za-z0-9_\-]*)(.*?)\]/i', $content, $tagMatches);
        for($i=0; $i < count($tagMatches[1]); $i++){
            $regex = '/(\S+)=[\'"]?((?:.(?![\'"]?\s+(?:\S+)=|[>\'"]))+.)[\'"]?/i';
            preg_match_all($regex, $tagMatches[3][$i], $attributes);
            $vocabularyAlias = $tagMatches[2][$i];
            $options = array();
            for($j=0; $j < count($attributes[0]); $j++){
                $options[$attributes[1][$j]] = $attributes[2][$j];
            }
            $content = str_replace($tagMatches[0][$i], $this->vocabulary($vocabularyAlias,$options), $content);
        }
        return $content;
    }

}
?>