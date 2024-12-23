<?php

namespace App\Entity;

use App\Repository\AdmiCaracteristicaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AdmiCaracteristicaRepository::class)
 */
class AdmiCaracteristica
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID_CARACTERISTICA", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $DESCRIPCION;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $TIPO;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $ESTADO;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $USR_CREACION;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $FE_CREACION;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getDESCRIPCION(): ?string
    {
        return $this->DESCRIPCION;
    }

    public function setDESCRIPCION(string $DESCRIPCION): self
    {
        $this->DESCRIPCION = $DESCRIPCION;

        return $this;
    }

    public function getTIPO(): ?string
    {
        return $this->TIPO;
    }

    public function setTIPO(string $TIPO): self
    {
        $this->TIPO = $TIPO;

        return $this;
    }

    public function getESTADO(): ?string
    {
        return $this->ESTADO;
    }

    public function setESTADO(string $ESTADO): self
    {
        $this->ESTADO = $ESTADO;

        return $this;
    }

    public function getUSRCREACION(): ?string
    {
        return $this->USR_CREACION;
    }

    public function setUSRCREACION(?string $USR_CREACION): self
    {
        $this->USR_CREACION = $USR_CREACION;

        return $this;
    }

    public function getFECREACION(): ?\DateTimeInterface
    {
        return $this->FE_CREACION;
    }

    public function setFECREACION(?\DateTimeInterface $FE_CREACION): self
    {
        $this->FE_CREACION = $FE_CREACION;

        return $this;
    }
}
