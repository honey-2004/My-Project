<?php
namespace Iovista\Blog\Model\ResourceModel\Post;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Iovista\Blog\Model\Post', 'Iovista\Blog\Model\ResourceModel\Post');
	}

}
