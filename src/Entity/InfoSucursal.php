<?php

namespace App\Entity;

use App\Repository\InfoSucursalRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * InfoSucursal
 * @ORM\Table(name="INFO_SUCURSAL")
 * @ORM\Entity(repositoryClass="App\Repository\InfoSucursalRepository")
 */
class InfoSucursal
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID_SUCURSAL", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
    * @var InfoEmpresa
    *
    * @ORM\ManyToOne(targetEntity="InfoEmpresa")
    * @ORM\JoinColumns({
    * @ORM\JoinColumn(name="EMPRESA_ID", referencedColumnName="ID_EMPRESA")
    * })
    */
    private $EMPRESA_ID;

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
     * @ORM\Column(type="string", length=255)
     */
    private $NOMBRE;

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
     * Get EMPRESA_ID
     *
     * @return \App\Entity\InfoEmpresa
     */
    public function getEMPRESAID()
    {
        return $this->EMPRESA_ID;
    }

    /**
     * Set setEMPRESAID
     *
     * @param \App\Entity\InfoEmpresa $EMPRESA_ID
     *
     * @return InfoSucursal
     */
    public function setEMPRESAID(\App\Entity\InfoEmpresa $EMPRESA_ID = null)
    {
        $this->EMPRESA_ID = $EMPRESA_ID;

        return $this;
    }
    /**
     * Get CLIENTE_ID
     *
     * @return \App\Entity\InfoCliente
     */
    public function getCLIENTEID()
    {
        return $this->CLIENTE_ID;
    }

    /**
     * Set setCLIENTEID
     *
     * @param \App\Entity\InfoCliente $CLIENTE_ID
     *
     * @return InfoSucursal
     */
    public function setCLIENTEID(\App\Entity\InfoCliente $CLIENTE_ID = null)
    {
        $this->CLIENTE_ID = $CLIENTE_ID;

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
