<?PHP

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * Home method controller
     *
     * @return Response
     *
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render(
            "home/home.html.twig",
            [
                "titre" => "SymBnb location",
            ]
        );
    }
}
