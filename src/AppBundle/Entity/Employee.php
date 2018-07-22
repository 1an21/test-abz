<?php

namespace AppBundle\Entity;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * Employee
 * @Gedmo\Tree(type="nested")
 * @ORM\Table(name="employee", indexes={@ORM\Index(name="parent", columns={"parent"})})
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\EmployeeRepository")
 */
class Employee
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_empl", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="surname", type="text", length=65535, nullable=false)
     */
    private $surname;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", length=65535, nullable=false)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="salary", type="integer", nullable=false)
     */
    private $salary;

    /**
     * @var string
     *
     * @ORM\Column(name="position", type="text", length=65535, nullable=false)
     */
    private $position;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="employmentDate", type="date", nullable=true)
     */
    private $employmentDate;

    /**
     * @var \Employee
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Employee", inversedBy="children")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent", referencedColumnName="id_empl",onDelete="CASCADE")
     * })
     */
    private $parent;
    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     */
    private $lft;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    private $lvl;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     */
    private $rgt;

    /**
     * @Gedmo\TreeRoot
     * @ORM\Column(name="root", type="integer", nullable=true)
     */
    private $root;
    /**
     * @ORM\OneToMany(targetEntity="Employee", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    protected $children;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set surname
     *
     * @param string $surname
     *
     * @return Employee
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Employee
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set salary
     *
     * @param integer $salary
     *
     * @return Employee
     */
    public function setSalary($salary)
    {
        $this->salary = $salary;

        return $this;
    }

    /**
     * Get salary
     *
     * @return integer
     */
    public function getSalary()
    {
        return $this->salary;
    }

    /**
     * Set position
     *
     * @param string $position
     *
     * @return Employee
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set employmentDate
     *
     * @param \DateTime $employmentDate
     *
     * @return Employee
     */
    public function setEmploymentDate($employmentDate)
    {
        $this->employmentDate = $employmentDate;

        return $this;
    }

    /**
     * Get employmentDate
     *
     * @return \DateTime
     */
    public function getEmploymentDate()
    {
        return $this->employmentDate;
    }

    /**
     * Set parent
     *
     * @param \AppBundle\Entity\Employee $parent
     *
     * @return Employee
     */
    public function setParent(\AppBundle\Entity\Employee $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \AppBundle\Entity\Employee
     */
    public function getParent()
    {
        return $this->parent;
    }
    public function getChildren() {
        return $this->children;
    }
}

