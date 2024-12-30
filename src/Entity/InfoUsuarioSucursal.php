<?php

 namespace App\Entity;

 use App\Repository\InfoUsuarioSucursalRepository;
 use Doctrine\ORM\Mapping as ORM;
 
 /**
  * InfoUsuarioSucursal
  * @ORM\Table(name="INFO_USUARIO_SUCURSAL")
  * @ORM\Entity(repositoryClass="App\Repository\InfoUsuarioSucursalRepository")
  */
class InfoUsuarioSucursal
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID_USUARIO_SUCURSAL", type="integer")
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
    * @var InfoSucursal
    *
    * @ORM\ManyToOne(targetEntity="InfoSucursal")
    * @ORM\JoinColumns({
    * @ORM\JoinColumn(name="SUCURSAL_ID", referencedColumnName="ID_SUCURSAL")
    * })
    */
    private $SUCURSAL_ID;

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
     * @return InfoUsuarioSucursal
     */
    public function setUSUARIOID(\App\Entity\InfoUsuario $USUARIO_ID = null)
    {
        $this->USUARIO_ID = $USUARIO_ID;

        return $this;
    }

    /**
     * Get SUCURSAL_ID
     *
     * @return \App\Entity\InfoSucursal
     */
    public function getSUCURSALID()
    {
        return $this->SUCURSAL_ID;
    }

    /**
     * Set setSUCURSALID
     *
     * @param \App\Entity\InfoSucursal $SUCURSAL_ID
     *
     * @return InfoUsuarioSucursal
     */
    public function setSUCURSALID(\App\Entity\InfoSucursal $SUCURSAL_ID = null)
    {
        $this->SUCURSAL_ID = $SUCURSAL_ID;

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
