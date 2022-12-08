<?php

namespace App\Entity;

use App\Repository\InfoClienteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * InfoCliente
 * @ORM\Table(name="INFO_CLIENTE")
 * @ORM\Entity(repositoryClass="App\Repository\InfoClienteRepository")
 */
class InfoCliente
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID_CLIENTE", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $IDENTIFICACION;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $NOMBRE;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $CORREO;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $CONTRASENIA;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    private $AUTENTICACION_RS;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $EDAD;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $GENERO;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $ESTADO;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $USR_CREACION;

    /**
     * @ORM\Column(type="datetime")
     */
    private $FE_CREACION;

    /**
     * @var string
     *
     * @ORM\Column(name="USR_MODIFICACION", type="string", length=255, nullable=true)
     */
    private $USR_MODIFICACION;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FE_MODIFICACION", type="datetime", nullable=true)
     */
    private $FE_MODIFICACION;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIDENTIFICACION(): ?string
    {
        return $this->IDENTIFICACION;
    }

    public function setIDENTIFICACION(?string $IDENTIFICACION): self
    {
        $this->IDENTIFICACION = $IDENTIFICACION;

        return $this;
    }

    public function getNOMBRE(): ?string
    {
        return $this->NOMBRE;
    }

    public function setNOMBRE(?string $NOMBRE): self
    {
        $this->NOMBRE = $NOMBRE;

        return $this;
    }

    public function getCORREO(): ?string
    {
        return $this->CORREO;
    }

    public function setCORREO(?string $CORREO): self
    {
        $this->CORREO = $CORREO;

        return $this;
    }

    public function getCONTRASENIA(): ?string
    {
        return $this->CONTRASENIA;
    }

    public function setCONTRASENIA(?string $CONTRASENIA): self
    {
        $this->CONTRASENIA = $CONTRASENIA;

        return $this;
    }

    public function getAUTENTICACIONRS(): ?string
    {
        return $this->AUTENTICACION_RS;
    }

    public function setAUTENTICACIONRS(?string $AUTENTICACION_RS): self
    {
        $this->AUTENTICACION_RS = $AUTENTICACION_RS;

        return $this;
    }

    public function getEDAD(): ?string
    {
        return $this->EDAD;
    }

    public function setEDAD(?string $EDAD): self
    {
        $this->EDAD = $EDAD;

        return $this;
    }

    public function getGENERO(): ?string
    {
        return $this->GENERO;
    }

    public function setGENERO(?string $GENERO): self
    {
        $this->GENERO = $GENERO;

        return $this;
    }

    public function getESTADO(): ?string
    {
        return $this->ESTADO;
    }

    public function setESTADO(?string $ESTADO): self
    {
        $this->ESTADO = $ESTADO;

        return $this;
    }

    public function getUSRCREACION(): ?string
    {
        return $this->USR_CREACION;
    }

    public function setUSRCREACION(string $USR_CREACION): self
    {
        $this->USR_CREACION = $USR_CREACION;

        return $this;
    }

    public function getFECREACION(): ?\DateTimeInterface
    {
        return $this->FE_CREACION;
    }

    public function setFECREACION(\DateTimeInterface $FE_CREACION): self
    {
        $this->FE_CREACION = $FE_CREACION;

        return $this;
    }

    public function getUSRMODIFICACION(): ?string
    {
        return $this->USR_MODIFICACION;
    }

    public function setUSRMODIFICACION(?string $USR_MODIFICACION): self
    {
        $this->USR_MODIFICACION = $USR_MODIFICACION;

        return $this;
    }

    public function getFEMODIFICACION(): ?\DateTimeInterface
    {
        return $this->FE_MODIFICACION;
    }

    public function setFEMODIFICACION(?\DateTimeInterface $FE_MODIFICACION): self
    {
        $this->FE_MODIFICACION = $FE_MODIFICACION;

        return $this;
    }
}
