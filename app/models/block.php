<?php
/**
 * Block
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
class Block extends AppModel {
/**
 * Model name
 *
 * @var string
 * @access public
 */
    var $name = 'Block';
/**
 * Behaviors used by the Model
 *
 * @var array
 * @access public
 */
    var $actsAs = array(
        'Encoder',
        'Ordered' => array('field' => 'weight', 'foreign_key' => 'region_id')
    );
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
 * Model associations: belongsTo
 *
 * @var array
 * @access public
 */
    var $belongsTo = array(
        'Region' => array(
            'className' => 'Region',
            'foreignKey' => 'region_id',
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'counterCache' => true,
            'counterScope' => array('Block.status' => 1),
        ),
    );

}
?>