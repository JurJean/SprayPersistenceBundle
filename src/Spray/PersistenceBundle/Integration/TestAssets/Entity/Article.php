<?php

namespace Spray\PersistenceBundle\Integration\TestAssets\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Article
 * 
 * @ORM\Entity
 */
class Article
{
    /**
     * @var integer
     * 
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     * @var string
     * 
     * @ORM\Column(type="string")
     */
    private $title;
    
    /**
     * @var string
     * 
     * @ORM\Column(type="datetime")
     */
    private $publishedAt;
    
    /**
     * Get the article identity
     * 
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Set article title
     * 
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = (string) $title;
    }
    
    /**
     * Get article title
     * 
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * Set the DateTime the article was published
     * 
     * @param null|DateTime $publishedAt
     * @return void
     */
    public function setPublishedAt(DateTime $publishedAt = null)
    {
        $this->publishedAt = $publishedAt;
    }
    
    /**
     * Get the DateTime the article was published, or null if it was not
     * published
     * 
     * @return null|DateTime
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }
}
