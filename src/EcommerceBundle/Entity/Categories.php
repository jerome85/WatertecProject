<?php

namespace EcommerceBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Translatable;

/**
 * Categories
 *
 * @ORM\Table("categories")
 * @ORM\Entity(repositoryClass="EcommerceBundle\Repository\CategoriesRepository")
 */
class Categories
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="EcommerceBundle\Entity\Media")
     * @ORM\JoinColumn(nullable=false)
     */
    private $image;
    
    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="name", type="string", length=125)
     */
    private $name;
    
    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    private $locale;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nom
     *
     * @param string $nom
     * @return Categories
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set image
     *
     * @param \EcommerceBundle\Entity\Media $image
     * @return Categories
     */
    public function setImage(\EcommerceBundle\Entity\Media $image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \EcommerceBundle\Entity\Media 
     */
    public function getImage()
    {
        return $this->image;
    }
    
    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }
    
    public function __toString()
    {
        return $this->getName();
    }        
            
}
