<?php

namespace App\Entity;

use App\Repository\InfoUsuarioRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * InfoCliente
 * @ORM\Table(name="INFO_USUARIO")
 * @ORM\Entity(repositoryClass="App\Repository\InfoUsuarioRepository")
 */
class InfoUsuario
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID_USUARIO", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
    * @var AdmiTipoRol
    *
    * @ORM\ManyToOne(targetEntity="AdmiTipoRol")
    * @ORM\JoinColumns({
    * @ORM\JoinColumn(name="TIPO_ROL_ID", referencedColumnName="ID_TIPO_ROL")
    * })
    */
    private $TIPO_ROL_ID;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $IDENTIFICACION;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $NOMBRE;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $APELLIDO;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $CONTRASENIA;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $CORREO;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $ESTADO;

    /**
     * @var string
     *
     * @ORM\Column(name="NOTIFICACION", type="string", length=2)
     */
    private $NOTIFICACION;

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

    /**
     * Get TIPO_ROL_ID
     *
     * @return \App\Entity\AdmiTipoRol
     */
    public function getTIPOROLID()
    {
        return $this->TIPO_ROL_ID;
    }

    /**
     * Set TIPO_ROL_ID
     *
     * @param \App\Entity\AdmiTipoRol $TIPO_ROL_ID
     *
     * @return InfoUsuario
     */
    public function setTIPOROLID(\App\Entity\AdmiTipoRol $TIPO_ROL_ID = null)
    {
        $this->TIPO_ROL_ID = $TIPO_ROL_ID;

        return $this;
    }

    public function getIDENTIFICACION(): ?string
    {
        return $this->IDENTIFICACION;
    }

    public function setIDENTIFICACION(string $IDENTIFICACION): self
    {
        $this->IDENTIFICACION = $IDENTIFICACION;

        return $this;
    }

    public function getNOMBRE(): ?string
    {
        return $this->NOMBRE;
    }

    public function setNOMBRE(string $NOMBRE): self
    {
        $this->NOMBRE = $NOMBRE;

        return $this;
    }

    public function getAPELLIDO(): ?string
    {
        return $this->APELLIDO;
    }

    public function setAPELLIDO(string $APELLIDO): self
    {
        $this->APELLIDO = $APELLIDO;

        return $this;
    }

    public function getCONTRASENIA(): ?string
    {
        return $this->CONTRASENIA;
    }

    public function setCONTRASENIA(string $CONTRASENIA): self
    {
        $this->CONTRASENIA = $CONTRASENIA;

        return $this;
    }

    public function getCORREO(): ?string
    {
        return $this->CORREO;
    }

    public function setCORREO(string $CORREO): self
    {
        $this->CORREO = $CORREO;

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

    /**
     * Set NOTIFICACION
     *
     * @param string $NOTIFICACION
     *
     * @return InfoUsuario
     */
    public function setNOTIFICACION($NOTIFICACION)
    {
        $this->NOTIFICACION = $NOTIFICACION;

        return $this;
    }

    /**
     * Get NOTIFICACION
     *
     * @return string
     */
    public function getNOTIFICACION()
    {
        return $this->NOTIFICACION;
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
