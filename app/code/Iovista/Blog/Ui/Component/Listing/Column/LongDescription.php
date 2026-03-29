<?php
namespace Iovista\Blog\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class LongDescription extends Column
{
    /**
     * Maximum length for truncated text
     */
    const MAX_LENGTH = 150;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item[$fieldName])) {
                    $text = $item[$fieldName];
                    // Strip HTML tags
                    $text = strip_tags($text);
                    // Decode HTML entities
                    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
                    // Truncate if too long
                    if (mb_strlen($text) > self::MAX_LENGTH) {
                        $text = mb_substr($text, 0, self::MAX_LENGTH) . '...';
                    }
                    $item[$fieldName] = $text;
                }
            }
        }
        return $dataSource;
    }
}


