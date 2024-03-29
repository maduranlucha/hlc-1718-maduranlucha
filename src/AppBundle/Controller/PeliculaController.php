<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Pelicula;
use AppBundle\Entity\Trailer;
use AppBundle\Entity\Usuario;
use AppBundle\Form\Type\PeliculaType;
use AppBundle\Form\Type\TrailerType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PeliculaController extends Controller
{
    /**
     * @Route("/cine", name="cinemostrar")
     */

    public function cineMostrar()
    {
        $peliculas = $this->mostrarPeliculas();
        return $this->render('cineworld/mostrar.html.twig',[
            'peliculas' => $peliculas
        ]);
    }

    /**
     * @Route("/cine/{id}", name="cinebuscar")
     */

    public function cineBuscar($id)
    {
        $peliculas = $this->mostrarPeliculas();

        return $this->render('cineworld/buscar.html.twig',[
            'pelicula' => $peliculas[$id]
        ]);
    }

    /**
     * @Route("/peliculas",name="peliculastodas")
     */

    public function listarPeliculas()
    {
        $peliculas = $this->getDoctrine()->getRepository('AppBundle:Pelicula')->findAll();
        return $this->render('cineworld/peliculas.html.twig',[
            'peliculas' => $peliculas
        ]);

    }

    /**
     * @Route("/comentarios",name="comentarios")
     */

    public function listarComentarios()
    {
        $comentarios = $this->getDoctrine()->getRepository('AppBundle:Comentario')->findAll();
        return $this->render('cineworld/comentarios.html.twig',[
            'comentarios' => $comentarios
        ]);
    }


    /**
     * @Route("/comentarios/{id}",name="comentariosusuario")
     */

    public function listarComentariosUsuario(Usuario $usuario)
    {
        return $this->render('cineworld/comentarios_usuarios.html.twig',[
            'usuario' => $usuario,
            'peliculas' => $usuario->getComentariosPropuestos()
        ]);
    }

    /**
     * @Route("/pelicula/{id}", name="pelicula")
     */

    public function peliculaSelect(Request $request, Pelicula $id)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(PeliculaType::class, $id);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em->flush();
                return $this->redirectToRoute('peliculastodas');
            }
            catch (\Exception $e) {
                $this->addFlash('error', 'No se han podido guardar los cambios');
            }
        }
        return $this->render(':cineworld:form.html.twig',[
            'pelicula' => $id,
            'formulario' => $form->createView()
        ]);
    }

    //Nueva Pelicula

    /**
     * @Route("/Pelicula/nueva", name="pelicula+")
     */
    public function nuevaAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $pelicula = new Pelicula();
        $em->persist($pelicula);

        $form = $this->createForm(PeliculaType::class,$pelicula);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em->flush();
                return $this->redirectToRoute('peliculastodas');
            }
            catch (\Exception $e) {
                $this->addFlash('error', 'Error al guardar los cambios');
            }
        }

        return $this->render('cineworld/form.html.twig', [
            'pelicula' => $pelicula,
            'formulario' => $form->createView()
        ]);
    }

    //ELIMINAR PELICULA

    /**
     * @Route("/Pelicula/eliminar/{id}", name="peliculaEliminar")
     */
    public function eliminarAction(Request $request, Pelicula $pelicula)
    {
        $em = $this->getDoctrine()->getManager();

        if ($request->isMethod('POST')) {
            try {

                foreach($pelicula->getTrailers() as $trailer) {
                    $em->remove($trailer);
                };

                $em->remove($pelicula);
                $em->flush();
                return $this->redirectToRoute('peliculastodas');
            }
            catch (\Exception $e) {
                $this->addFlash('error', 'No se ha podido eliminar');
            }
        }

        return $this->render(':cineworld:eliminarPelicula.html.twig', [
            'pelicula' => $pelicula
        ]);
    }

    //TRAILERS


    /**
     * @Route("/trailers",name="trailers")
     */

    public function listarTrailers()
    {
        $trailerss = $this->getDoctrine()->getRepository('AppBundle:Trailer')->findAll();
        return $this->render('cineworld/trailers.html.twig',[
            'trailerss' => $trailerss
        ]);

    }

    /**
     * @Route("/Trailer/{id}", name="trailer")
     */

    public function trailerSelect(Request $request, Trailer $id)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(TrailerType::class, $id);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em->flush();
                return $this->redirectToRoute('trailers');
            }
            catch (\Exception $e) {
                $this->addFlash('error', 'No se han podido guardar los cambios');
            }
        }
        return $this->render(':cineworld:formTrailer.html.twig',[
            'trailer' => $id,
            'formulario' => $form->createView()
        ]);
    }

    //TRAILER+

    /**
     * @Route("/Trailers/nuevo", name="trailermas")
     */
    public function trailerNu(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $trailer = new Trailer();
        $em->persist($trailer);

        $form = $this->createForm(TrailerType::class,$trailer);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em->flush();
                return $this->redirectToRoute('trailers');
            }
            catch (\Exception $e) {
                $this->addFlash('error', 'Error al guardar los cambios');
            }
        }

        return $this->render(':cineworld:formTrailer.html.twig', [
            'trailer' => $trailer,
            'formulario' => $form->createView()
        ]);
    }

    //ELIMINAR TRAILER

    /**
     * @Route("/Trailer/eliminar/{id}", name="trailerEliminar")
     */
    public function eliminarAction2(Request $request, Trailer $trailer)
    {
        $em = $this->getDoctrine()->getManager();

        if ($request->isMethod('POST')) {
            try {

                $em->remove($trailer);
                $em->flush();
                return $this->redirectToRoute('trailers');
            }
            catch (\Exception $e) {
                $this->addFlash('error', 'No se ha podido eliminar');
            }
        }

        return $this->render(':cineworld:eliminarTrailer.html.twig', [
            'trailer' => $trailer
        ]);
    }

    //FUNCIONES COMPARTIDAS

    private function mostrarPeliculas()
    {
        Return [

            1=> ['id'=> 1,'titulo' =>'Armageddon','director' => 'Michael Bay','genero' => 'Ciencia Ficcion','duracion' => '150 Minutos', 'calidad' => 'DVD','imagen' => 'img/armageddon.jpg', 'resumen' => 'La película comienza en la Extinción masiva del Cretácico-Terciario, hace 65 millones de años, al final del periodo Cretácico, cuando un meteorito de 10 kilómetros impacta en la Tierra y acaba con el reinado de los dinosaurios y los otros grandes reptiles. El narrador dice "sucedió, y volverá a pasar. La pregunta es cuando".

Millones de años después, el Transbordador Atlantis estaba en el espacio con tripulación, comunicándose con la NASA, cuando de repente cientos de cosas golpean la nave y hacen que explote. Después en Nueva York un chico se estaba peleaba con un comerciante cuando un meteorito cae y lo asesina, el perro del chico vive, pero unos segundos después cientos de meteoritos (de los mismos que destruyeron el Transbordador Atlantis) caen en la ciudad destruyéndola casi por completo. después de aquel suceso, el director de la NASA descubre un gran asteroide del tamaño de Texas. pero lo peor es que aquel inmenso asteroide impactara en la Tierra dentro de 18 días.'],
            2=> ['id'=> 2,'titulo' =>'Salvar al soldado Ryan','director' => 'Steven Spilverg','genero' => 'Belico','duracion' => '169 Minutos', 'calidad' => 'BlueRay', 'imagen' => 'img/ryan.jpg','resumen' =>'En la mañana del 6 de junio de 1944, comienzo de la invasión de Normandía, los soldados estadounidenses se preparan para desembarcar en la playa de Omaha. Nada más abrirse las puertas de sus lanchas de desembarco son recibidos por un feroz fuego de artillería alemana, que masacra a muchos de los soldados en cuanto ponen pie en tierra. El capitán John H. Miller, al mando de la compañía Charlie del 2.º Batallón Ranger, sobrevive a la carnicería del desembarco, reúne a un grupo de soldados para intentar penetrar las defensas alemanas y abre brecha para avanzar desde la playa.'],
            3=> ['id'=> 3,'titulo' =>'La lista de Schindler','director' => 'Steven Spilverg','genero' => 'Drama','duracion' => '196 Minutos', 'calidad' => 'DVD', 'imagen' => 'img/lista.jpg','resumen' =>'En Cracovia, durante la Segunda Guerra Mundial, las tropas alemanas de ocupación han forzado a los judíos polacos a vivir recluidos en un gueto. El empresario Oskar Schindler, de etnia alemana y miembro del partido nazi, llega a la ciudad decidido a hacer fortuna y comienza por sobornar a diversos oficiales de las fuerzas armadas alemanas y de las SS. Asimismo, adquiere una fábrica para producir menaje esmaltado. Para ayudarlo en la gestión del negocio, contrata a Itzhak Stern, un contable judío que tiene contactos en el mercado negro y en la comunidad local de empresarios hebreos y que le ayuda a financiar la factoría. Schindler mantiene relaciones amistosas con los nazis y disfruta de cierta riqueza y estatus social como «Herr Direktor», mientras Stern se ocupa de la administración. Ambos contratan empleados judíos porque sus sueldos son inferiores por imposición alemana y porque Stern busca salvar a su pueblo de la deportación a los campos de concentración convirtiéndolos en trabajadores esenciales para el esfuerzo de guerra alemán.'],
            4=> ['id'=> 4,'titulo' =>'Terminator','director' => 'James Cameron','genero' => 'Ciencia Ficcion','duracion' => '108 Minutos', 'calidad' => 'UHD', 'imagen' => 'img/terminator.jpg','resumen' =>'En el año 2029, después de devastar la Tierra y esclavizar a la humanidad, las máquinas, gobernadas por la inteligencia artificial conocida como Skynet, están a punto de perder la guerra contra la resistencia humana liderada por John Connor. Frente a esa situación, las máquinas entienden que asesinar a John Connor en el presente, sería irrelevante, dado que ya ha conducido a la resistencia humana del mundo entero a la victoria. Por lo tanto, Skynet elabora su estrategia decidiendo eliminar al líder enemigo antes de que éste nazca, de modo que no pueda cumplirse su misión de conducción futura. Para ello envía al pasado (año 1984) a un Terminator T-800 modelo Cyberdyne 101, un cíborg asesino (Arnold Schwarzenegger), a través de una máquina del tiempo, con la misión de exterminar a Sarah Connor (Linda Hamilton), madre de John, antes de que éste sea concebido.'],

        ];
    }
}
