<?php
namespace App\Message;


class AddPonkaToImage
{
    private $imagePostId;

    public function __construct(int $id)
   {
       $this->imagePostId = $id;
   }

    public function getImagePostId(): int
    {
        return $this->imagePostId;
    }
}