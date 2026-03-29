<?php
namespace Iovista\Blog\Model;
class Post extends \Magento\Framework\Model\AbstractModel
{
	protected function _construct()
	{
		$this->_init('Iovista\Blog\Model\ResourceModel\Post');
	}
}