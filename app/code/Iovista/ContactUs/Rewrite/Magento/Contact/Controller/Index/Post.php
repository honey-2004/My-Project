<?php
namespace Iovista\ContactUs\Rewrite\Magento\Contact\Controller\Index;

use Iovista\ContactUs\Rewrite\Magento\Framework\Mail\Template\TransportBuilder;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Contact\Model\ConfigInterface;
use Magento\Contact\Model\MailInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Area;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Translate\Inline\StateInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Validation\ValidationException;

/**
 * Post controller class
 */
class Post extends \Magento\Contact\Controller\Index\Post
{
    const FOLDER_LOCATION = 'contactattachment';

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var ConfigInterface
     */
    private $contactsConfig;

    /**
     * @var MailInterface
     */
    private $mail;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var File
     */
    private $file;

    /**
     * @var UploaderFactory
     */
    private $fileUploaderFactory;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @var StateInterface
     */
    private $inlineTranslation;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        Context                         $context,
        MailInterface                   $mail,
        DataPersistorInterface $dataPersistor,
        ?LoggerInterface                $logger,
        UploaderFactory        $fileUploaderFactory,
        Filesystem             $fileSystem,
        StateInterface         $inlineTranslation,
        ConfigInterface        $contactsConfig,
        TransportBuilder       $transportBuilder,
        StoreManagerInterface  $storeManager,
        File $file
    ) {
        $this->logger = $logger ?: ObjectManager::getInstance()->get(LoggerInterface::class);
        $this->contactsConfig = $contactsConfig;
        $this->dataPersistor = $dataPersistor;
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->fileSystem = $fileSystem;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->file = $file;
        parent::__construct($context, $contactsConfig, $mail, $dataPersistor, $logger);
    }

    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }
        try {
            $this->sendEmail($this->validatedParams());

            $this->dataPersistor->clear('contact_us');
            return $this->resultRedirectFactory->create()->setPath('contact-us-acknowledge');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->dataPersistor->set('contact_us', $this->getRequest()->getParams());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $this->messageManager->addErrorMessage(
                __('An error occurred while processing your form. Please try again later.')
            );
            $this->dataPersistor->set('contact_us', $this->getRequest()->getParams());
        }
        return $this->resultRedirectFactory->create()->setPath('contact/');
    }

    private function sendEmail($post)
    {
        $this->send(
            $post['email'],
            ['data' => new DataObject($post)]
        );
    }

    public function send($replyTo, array $variables)
    {
        $filePath = null;
        $fileName = null;
        $uploaded = false;

        try {
            // Check if a file is uploaded
            if (!empty($_FILES['attachment']['name'])) {
                $fileCheck = $this->fileUploaderFactory->create(['fileId' => 'attachment']);
                $file = $fileCheck->validateFile();
                $attachment = $file['name'] ?? null;

                if ($attachment) {
                    $upload = $this->fileUploaderFactory->create(['fileId' => 'attachment']);
                    $upload->setAllowRenameFiles(true);
                    $upload->setFilesDispersion(true);
                    $upload->setAllowCreateFolders(true);
                    $upload->setAllowedExtensions(['jpg', 'jpeg', 'png']);

                    $path = $this->fileSystem
                        ->getDirectoryRead(DirectoryList::MEDIA)
                        ->getAbsolutePath(self::FOLDER_LOCATION);
                    $result = $upload->save($path);
                    $uploaded = self::FOLDER_LOCATION . $upload->getUploadedFilename();
                    $filePath = $result['path'] . $result['file'];
                    $fileName = $result['name'];
                }
            }
        } catch (\Exception $e) {
            // Log the error but do not stop form submission
            $this->logger->error("File upload error: " . $e->getMessage());
        }

        $replyToName = !empty($variables['data']['name']) ? $variables['data']['name'] : null;

        $this->inlineTranslation->suspend();

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $transportBuilder = $this->transportBuilder
            ->setTemplateIdentifier($this->contactsConfig->emailTemplate())
            ->setTemplateOptions(
                [
                    'area' => Area::AREA_FRONTEND,
                    'store' => $this->storeManager->getStore()->getId(),
                ]
            )
            ->setTemplateVars($variables)
            ->setFrom($this->contactsConfig->emailSender())
            ->addTo($this->contactsConfig->emailRecipient())
            ->setReplyTo($replyTo, $replyToName);

        // Add attachment if it exists
        if ($uploaded && !empty($filePath) && $this->file->fileExists($filePath)) {
            $mimeType = mime_content_type($filePath);
            $transportBuilder->addAttachment($this->file->read($filePath), $fileName, $mimeType);
        }

        $transport = $transportBuilder->getTransport();
        $transport->sendMessage();

        $this->inlineTranslation->resume();
    }

    private function validatedParams(): array
    {
        $request = $this->getRequest();
        return $request->getParams();
    }
}
