<?php

namespace App\Entity;

use App\Repository\InfoPromocionHistorialRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * InfoPromocionHistorial
 * @ORM\Table(name="INFO_CLIENTE_PROMOCION_HISTORIAL")
 * @ORM\Entity(repositoryClass="App\Repository\InfoPromocionHistorialRepository")
 */
class InfoPromocionHistorial
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID_CLIENTE_PUNTO_HISTORIAL", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
    * @var InfoPromocion
    *
    * @ORM\ManyToOne(targetEntity="InfoPromocion")
    * @ORM\JoinColumns({
    * @ORM\JoinColumn(name="PROMOCION_ID", referencedColumnName="ID_PROMOCION")
    * })
    */
    private $PROMOCION_ID;

    /**
    * @var InfoCliente
    *
    * @ORM\ManyToOne(targetEntity="InfoCliente")
    * @ORM\JoinColumns({
    * @ORM\JoinColumn(name="CLIENTE_ID", referencedColumnName="ID_CLIENTE")
    * })
    */
    private $CLIENTE_ID;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set PROMOCIONID
     *
     * @param \App\Entity\InfoPromocion $PROMOCIONID
     *
     * @return InfoPromocionHistorial
     */
    public function setPROMOCIONID(\App\Entity\InfoPromocion $PROMOCIONID = null)
    {
        $this->PROMOCION_ID = $PROMOCIONID;

        return $this;
    }

    /**
     * Get PROMOCIONID
     *
     * @return \App\Entity\InfoPromocion
     */
    public function getPROMOCIONID()
    {
        return $this->PROMOCION_ID;
    }

    /**
     * Set CLIENTEID
     *
     * @param \App\Entity\InfoCliente $CLIENTEID
     *
     * @return InfoPromocionHistorial
     */
    public function setCLIENTEID(\App\Entity\InfoCliente $CLIENTEID = null)
    {
        $this->CLIENTE_ID = $CLIENTEID;

        return $this;
    }

    /**
     * Get CLIENTEID
     *
     * @return \App\Entity\InfoCliente
     */
    public function getCLIENTEID()
    {
        return $this->CLIENTE_ID;
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
