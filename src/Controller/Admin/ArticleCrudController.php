<?php
namespace App\Controller\Admin;

use App\Entity\Article;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ArticleCrudController extends AbstractCrudController
{
    public CONST ARTICLES_BASE_PATH = 'assets/images';
    public CONST ARTICLES_UPLOAD_DIR = 'public/assets/images';
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title', 'Titre'),
            TextField::new('subtitle', 'Sous-titre'),
            ImageField::new('picture', 'Photo')
                ->setBasePath(self::ARTICLES_BASE_PATH)
                ->setUploadDir(self::ARTICLES_UPLOAD_DIR)
                ->setUploadedFileNamePattern('[contenthash].[extension]'),
            TextEditorField::new('content', 'Contenu de l\'article'),
            DateTimeField::new('createdAt')->hideOnForm(),
            DateTimeField::new('updatedAt')->hideOnForm(),
        ];
    }
}
