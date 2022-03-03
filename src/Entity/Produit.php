<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Vich\UploaderBundle\Mapping\Annotation\Uploadable;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
/**
 * @Vich\Uploadable
 */
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message:"Veuillez ajouter un nom à votre article")]
    private $nom;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message:"Veuillez ajouter une description")]
    private $description;

    #[ORM\Column(type: 'float')]
    #[Assert\NotBlank(message:"Veuillez ajouter un prix")]
    private $prix;

    #[ORM\Column(type: 'string', length: 255)]
    private $image;

     /**
     * @Vich\UploadableField(mapping="produits_images", fileNameProperty="image")
     */
    private $imageFile;

    #[ORM\ManyToOne(targetEntity: Categorie::class, inversedBy: 'produits')]
    private $categorie;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'produits')]
    private $auteur;

    #[ORM\OneToMany(mappedBy: 'produit', targetEntity: Commentaire::class)]
    private $commentaires;

    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getImage():?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getAuteur(): ?User
    {
        return $this->auteur;
    }

    public function setAuteur(?User $auteur): self
    {
        $this->auteur = $auteur;

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires[] = $commentaire;
            $commentaire->setProduit($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getProduit() === $this) {
                $commentaire->setProduit(null);
            }
        }

        return $this;
    }

    public function getImageFile(): ?File 
    {
        return $this->imageFile; 
    }
         
    public function setImageFile(?File $imageFile = null): self 
    {
    $this->imageFile = $imageFile;

        if($this->imageFile instanceof UploadedFile)
        {
            // on met à jour sa date de mise à jour
            $this->updateAt = new \DateTime();
        }
    return $this; 
    }
}