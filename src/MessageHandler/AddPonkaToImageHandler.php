<?php

namespace App\MessageHandler;

use App\Message\AddPonkaToImage;
use App\Photo\PhotoFileManager;
use App\Photo\PhotoPonkaficator;
use App\Repository\ImagePostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AddPonkaToImageHandler implements MessageHandlerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private $photoPonkaficator;
    private $photoManager;
    private $entityManager;
    private $imagePostRepository;

    public function __construct(
        PhotoPonkaficator $photoPonkaficator,
        PhotoFileManager $photoManager,
        EntityManagerInterface $entityManager,
        ImagePostRepository $imagePostRepository
    )
    {
        $this->photoPonkaficator = $photoPonkaficator;
        $this->photoManager = $photoManager;
        $this->entityManager = $entityManager;
        $this->imagePostRepository = $imagePostRepository;
    }

    public function __invoke(AddPonkaToImage $addPonkaToImage)
    {
        $imagePostId = $addPonkaToImage->getImagePostId();
        $imagePost = $this->imagePostRepository->find($imagePostId);

        if (!$imagePost) {
            if ($this->logger) {
                $this->logger->alert(sprintf('Image post with id %d is missing', $imagePostId));
            }

            return;
        }

        $updatedContents = $this->photoPonkaficator->ponkafy(
            $this->photoManager->read($imagePost->getFilename())
        );
        $this->photoManager->update($imagePost->getFilename(), $updatedContents);
        $imagePost->markAsPonkaAdded();
        $this->entityManager->flush();
    }
}