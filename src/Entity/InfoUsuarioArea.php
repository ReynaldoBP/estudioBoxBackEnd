<?php

namespace App\Entity;

use App\Repository\InfoUsuarioAreaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * InfoUsuarioArea
 * @ORM\Table(name="INFO_USUARIO_AREA")
 * @ORM\Entity(repositoryClass="App\Repository\InfoUsuarioAreaRepository")
 */
class InfoUsuarioArea
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID_USUARIO_AREA", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
    * @var InfoUsuario
    *
    * @ORM\ManyToOne(targetEntity="InfoUsuario")
    * @ORM\JoinColumns({
    * @ORM\JoinColumn(name="USUARIO_ID", referencedColumnName="ID_USUARIO")
    * })
    */
    private $USUARIO_ID;

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
     * Get USUARIO_ID
     *
     * @return \App\Entity\InfoUsuario
     */
    public function getUSUARIOID()
    {
        return $this->USUARIO_ID;
    }

    /**
     * Set setUSUARIOID
     *
     * @param \App\Entity\InfoUsuario $USUARIO_ID
     *
     * @return InfoUsuarioEmpresa
     */
    public function setUSUARIOID(\App\Entity\InfoUsuario $USUARIO_ID = null)
    {
        $this->USUARIO_ID = $USUARIO_ID;

        return $this;
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
     * @return InfoUsuarioEmpresa
     */
    public function setAREAID(\App\Entity\InfoArea $AREA_ID = null)
    {
        $this->AREA_ID = $AREA_ID;

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
