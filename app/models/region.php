<?php
/**
 * Region
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
class Region extends AppModel {
/**
 * Model name
 *
 * @var string
 * @access public
 */
    var $name = 'Region';
/**
 * Validation
 *
 * @var array
 * @access public
 */
    var $validate = array(
        'alias' => array(
            'rule' => 'isUnique',
            'message' => 'Alias is already in use.',
        ),
    );
/**
 * Model associations: hasMany
 *
 * @var array
 * @access public
 */
    var $hasMany = array(
            'Block' => array('className' => 'Block',
                                'foreignKey' => 'region_id',
                                'dependent' => false,
                                'conditions' => '',
                                'fields' => '',
                                'order' => '',
                                'limit' => '3',
                                'offset' => '',
                                'exclusive' => '',
                                'finderQuery' => '',
                                'counterQuery' => ''
            )
        );
}
?>