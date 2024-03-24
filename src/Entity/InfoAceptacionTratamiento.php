<?php

namespace App\Entity;

use App\Repository\InfoAceptacionTratamientoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * InfoAceptacionTratamiento
 * @ORM\Table(name="INFO_ACEPTACION_TRATAMIENTO")
 * @ORM\Entity(repositoryClass="App\Repository\InfoAceptacionTratamientoRepository")
 */
class InfoAceptacionTratamiento
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID_ACEPTACION_TRATAMIENTO", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
    * @var InfoTratamientoDatosPersonales
    *
    * @ORM\ManyToOne(targetEntity="InfoTratamientoDatosPersonales")
    * @ORM\JoinColumns({
    * @ORM\JoinColumn(name="TRATAMIENTO_DATOS_PERSONALES_ID", referencedColumnName="ID_TRATAMIENTO_DATOS_PERSONALES")
    * })
    */
    private $TRATAMIENTO_DATOS_PERSONALES_ID;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $IDENTIFICACION;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $FIRMA;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $CORREO;

    /**
     * @ORM\Column(type="string", length=50)
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

    /**
     * Get TRATAMIENTO_DATOS_PERSONALES_ID
     *
     * @return \App\Entity\InfoTratamientoDatosPersonales
     */
    public function getTRATAMIENTODATOSPERSONALESID()
    {
        return $this->TRATAMIENTO_DATOS_PERSONALES_ID;
    }

    /**
     * Set setTRATAMIENTODATOSPERSONALESID
     *
     * @param \App\Entity\InfoTratamientoDatosPersonales $TRATAMIENTO_DATOS_PERSONALES_ID
     *
     * @return InfoTratamientoDatosPersonales
     */
    public function setTRATAMIENTODATOSPERSONALESID(\App\Entity\InfoTratamientoDatosPersonales $TRATAMIENTO_DATOS_PERSONALES_ID = null)
    {
        $this->TRATAMIENTO_DATOS_PERSONALES_ID = $TRATAMIENTO_DATOS_PERSONALES_ID;

        return $this;
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

    public function getFirma(): ?string
    {
        return $this->FIRMA;
    }

    public function setFirma(string $FIRMA): self
    {
        $this->FIRMA = $FIRMA;

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
