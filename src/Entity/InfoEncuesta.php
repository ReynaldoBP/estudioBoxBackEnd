<?php

namespace App\Entity;

use App\Repository\InfoEncuestaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * InfoEncuesta
 * @ORM\Table(name="INFO_ENCUESTA")
 * @ORM\Entity(repositoryClass="App\Repository\InfoEncuestaRepository")
 */
class InfoEncuesta
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID_ENCUESTA", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
    * @var InfoArea
    *
    * @ORM\ManyToOne(targetEntity="InfoArea")
    * @ORM\JoinColumns({
    * @ORM\JoinColumn(name="AREA_ID", referencedColumnName="ID_AREA")
    * })
    */
    private $AREA_ID;

    /**
     * @var string
     *
     * @ORM\Column(name="DESCRIPCION", type="string", length=255)
     */
    private $DESCRIPCION;

    /**
     * @var string
     *
     * @ORM\Column(name="TITULO", type="string", length=255, nullable=true)
     */
    private $TITULO;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private $PERMITE_FIRMA;

/**
     * @ORM\Column(type="string", length=2)
     */
    private $PERMITE_DATO_ADICIONAL;

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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get AREA_ID
     *
     * @return \App\Entity\InfoArea
     */
    public function getAREAID()
    {
        return $this->AREA_ID;
    }

    /**
     * Set setAREAID
     *
     * @param \App\Entity\InfoArea $AREA_ID
     *
     * @return InfoPregunta
     */
    public function setAREAID(\App\Entity\InfoArea $AREA_ID = null)
    {
        $this->AREA_ID = $AREA_ID;

        return $this;
    }

    /**
     * Set DESCRIPCION
     *
     * @param string $DESCRIPCION
     *
     * @return InfoEncuesta
     */
    public function setDESCRIPCION($DESCRIPCION)
    {
        $this->DESCRIPCION = $DESCRIPCION;

        return $this;
    }

    /**
     * Get DESCRIPCION
     *
     * @return string
     */
    public function getDESCRIPCION()
    {
        return $this->DESCRIPCION;
    }

    /**
     * Set TITULO
     *
     * @param string $TITULO
     *
     * @return InfoEncuesta
     */
    public function setTITULO($TITULO)
    {
        $this->TITULO = $TITULO;

        return $this;
    }

    /**
     * Get TITULO
     *
     * @return string
     */
    public function getTITULO()
    {
        return $this->TITULO;
    }

    public function getPERMITE_FIRMA(): ?string
    {
        return $this->PERMITE_FIRMA;
    }

    public function setPERMITE_FIRMA(string $PERMITE_FIRMA): self
    {
        $this->PERMITE_FIRMA = $PERMITE_FIRMA;

        return $this;
    }

    public function getPERMITE_DATO_ADICIONAL(): ?string
    {
        return $this->PERMITE_DATO_ADICIONAL;
    }

    public function setPERMITE_DATO_ADICIONAL(string $PERMITE_DATO_ADICIONAL): self
    {
        $this->PERMITE_DATO_ADICIONAL = $PERMITE_DATO_ADICIONAL;

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
