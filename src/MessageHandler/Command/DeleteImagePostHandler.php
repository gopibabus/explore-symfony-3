<?php
namespace App\MessageHandler\Command;

use App\Message\Command\DeleteImagePost;
use App\Message\Event\ImagePostDeletedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class DeleteImagePostHandler implements MessageSubscriberInterface
{
    private $entityManager;
    private $eventBus;

    public function __construct(
        EntityManagerInterface $entityManager,
        MessageBusInterface $eventBus
    )
    {
        $this->entityManager = $entityManager;
        $this->eventBus = $eventBus;
    }

    public function __invoke(DeleteImagePost $deleteImagePost)
    {
        $imagePost = $deleteImagePost->getImagePost();
        $fileName = $imagePost->getFilename();

        $this->entityManager->remove($imagePost);
        $this->entityManager->flush();

        $this->eventBus->dispatch(new ImagePostDeletedEvent($fileName));
    }

    /**
     * This method is responsible for defining default method that symfony look for
     * and define Bus Class related to handler
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield DeleteImagePost::class => [
          'method' => '__invoke',
          'priority' => 10
        ];
    }

}