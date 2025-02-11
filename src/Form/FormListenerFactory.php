<?php
namespace App\Form;

use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Form\FormEvents;
use App\Entity\Category;

class FormListenerFactory
{
    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function autoSlug(string $field): callable
    {
        return function(PreSubmitEvent $event) use ($field) {
            $data = $event->getData();
            if (isset($data[$field]) && empty($data['slug'])) {
                $data['slug'] = strtolower($this->slugger->slug($data[$field]));
                $event->setData($data);
            }
        };
    }

    public function timestamps(): callable
    {
        return function(PostSubmitEvent $event) {
            $data = $event->getData();
            if (!$data instanceof Category) { 
                return;
            }

            $data->setUpdatedAt(new \DateTimeImmutable());

            if (!$data->getId()) {
                $data->setCreatedAt(new \DateTimeImmutable());
            }
        };
    }
}
