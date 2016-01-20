<?php

namespace Model\Page;

/**
 * Model storing all file uses within particular web application.
 * 
 * @Entity
 * @Table(name="pages_images")
 * 
 * @author		Krzysztof Trzos
 * @copyright	(c) 2015, Krzysztof Trzos
 * @package		pages
 * @subpackage	Model\Page
 * @since		1.1.4-dev, 2015-08-02
 * @version		1.1.5-dev, 2015-08-09
 */
class Image extends \Plethora\ModelCore\FileBroker {

	/**
	 * A parent to which this file is corresponding.
	 * 
	 * @ManyToOne(targetEntity="\Model\Page", inversedBy="image")
	 * @JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
	 * 
	 * @access	protected
	 * @var		\Model\Page
	 * @since	1.1.4-dev, 2015-08-02
	 */
	protected $parent;

	/**
	 * Get parent to which this file is corresponding.
	 * 
	 * @access	public
	 * @return	\Model\Page
	 * @since	1.1.4-dev, 2015-08-02
	 * @version	1.1.4-dev, 2015-08-02
	 */
	public function getParent() {
		return $this->parent;
	}

	/**
	 * Set parent to which this file is corresponding.
	 * 
	 * @access	public
	 * @param	\Model\Page $parent
	 * @return	$this
	 * @since	1.1.4-dev, 2015-08-02
	 * @version	1.1.4-dev, 2015-08-02
	 */
	public function setParent($parent) {
		$this->parent = $parent;

		return $this;
	}
}