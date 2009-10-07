<?php
/**
 * Menu
 *
 * PHP version 5
 *
 * @category Model
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class Menu extends AppModel {
/**
 * Model name
 *
 * @var string
 * @access public
 */
    var $name = 'Menu';
/**
 * Validation
 *
 * @var array
 * @access public
 */
    var $validate = array(
        'alias' => array(
            'rule' => 'isUnique',
            'message' => 'This alias has already been taken.',
        ),
    );
/**
 * Model associations: hasMany
 *
 * @var array
 * @access public
 */
    var $hasMany = array(
            'Link' => array('className' => 'Link',
                                'foreignKey' => 'menu_id',
                                'dependent' => false,
                                'conditions' => '',
                                'fields' => '',
                                'order' => 'Link.lft ASC',
                                'limit' => '',
                                'offset' => '',
                                'exclusive' => '',
                                'finderQuery' => '',
                                'counterQuery' => ''
            )
        );
}
?>