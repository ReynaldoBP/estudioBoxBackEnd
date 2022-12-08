<?php

namespace App\Entity;

use App\Repository\InfoModuloAccionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * InfoModuloAccion
 * @ORM\Table(name="INFO_MODULO_ACCION")
 * @ORM\Entity(repositoryClass="App\Repository\InfoModuloAccionRepository")
 */
class InfoModuloAccion
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID_MODULO_ACCION", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
    * @var AdmiModulo
    *
    * @ORM\ManyToOne(targetEntity="AdmiModulo")
    * @ORM\JoinColumns({
    * @ORM\JoinColumn(name="MODULO_ID", referencedColumnName="ID_MODULO")
    * })
    */
    private $MODULO_ID;

    /**
    * @var AdmiAccion
    *
    * @ORM\ManyToOne(targetEntity="AdmiAccion")
    * @ORM\JoinColumns({
    * @ORM\JoinColumn(name="ACCION_ID", referencedColumnName="ID_ACCION")
    * })
    */
    private $ACCION_ID;

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

    public function getDESCRIPCION(): ?string
    {
        return $this->DESCRIPCION;
    }

    public function setDESCRIPCION(string $DESCRIPCION): self
    {
        $this->DESCRIPCION = $DESCRIPCION;

        return $this;
    }


    /**
     * Set MODULOID
     *
     * @param \App\Entity\AdmiModulo $MODULOID
     *
     * @return InfoModuloAccion
     */
    public function setMODULOID(\App\Entity\AdmiModulo $MODULOID = null)
    {
        $this->MODULO_ID = $MODULOID;

        return $this;
    }

    /**
     * Get MODULOID
     *
     * @return \App\Entity\AdmiModulo
     */
    public function getMODULOID()
    {
        return $this->MODULO_ID;
    }

    /**
     * Set ACCIONID
     *
     * @param \App\Entity\AdmiAccion $ACCIONID
     *
     * @return InfoModuloAccion
     */
    public function setACCIONID(\App\Entity\AdmiAccion $ACCIONID = null)
    {
        $this->ACCION_ID = $ACCIONID;

        return $this;
    }

    /**
     * Get ACCIONID
     *
     * @return \App\Entity\AdmiAccion
     */
    public function getACCIONID()
    {
        return $this->ACCION_ID;
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
