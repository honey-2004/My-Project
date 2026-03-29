<?php

namespace Iovista\SupportForm\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\ScopeInterface;

class Post extends Action
{
    protected $resultJsonFactory;
    protected $transportBuilder;
    protected $scopeConfig;
    protected $inlineTranslation;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        TransportBuilder $transportBuilder,
        ScopeConfigInterface $scopeConfig,
        StateInterface $inlineTranslation
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->inlineTranslation = $inlineTranslation;
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        try {
            $data = $this->getRequest()->getPostValue();

            if (!$data) {
                return $result->setData(['success' => false]);
            }

            $recipientEmail = $this->scopeConfig->getValue(
                'contact/email/recipient_email',
                ScopeInterface::SCOPE_STORE
            );

            $senderIdentity = $this->scopeConfig->getValue(
                'contact/email/sender_email_identity',
                ScopeInterface::SCOPE_STORE
            );

            $this->inlineTranslation->suspend();

            $transport = $this->transportBuilder
                ->setTemplateIdentifier('iovista_support_email_template') 
                ->setTemplateOptions([
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => 1
                ])
                ->setTemplateVars([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'telephone' => $data['phone'],
                    'topic' => $data['topic'] ?? '',
                    'comment' => $data['message']
                ])
                ->setFromByScope($senderIdentity)
                ->addTo($recipientEmail)
                ->setReplyTo($data['email'])
                ->getTransport();

            $transport->sendMessage();

            $this->inlineTranslation->resume();

            return $result->setData(['success' => true]);

        } catch (\Exception $e) {
            return $result->setData(['success' => false]);
        }
    }
}