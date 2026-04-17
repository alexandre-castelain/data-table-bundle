# Query

The data table requires a query to fetch the data. This query can be passed directly to the factory or defined as a default option in the data table type.

[[toc]]

## Ways to handle the query

There are several ways to pass the query to a DataTable.

### Passing the query to the factory

The query can be passed directly to the factory in the controller:

```php
use App\DataTable\Type\ProductDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;
    
    public function index(Request $request): Response
    {
        // $query can be a QueryBuilder, an array, etc.
        $dataTable = $this->createDataTable(ProductDataTableType::class, $query);
        $dataTable->handleRequest($request);
        
        return $this->render('product/index.html.twig', [
            'products' => $dataTable->createView(),
        ]);
    }
}
```

### Defining a default value

You can provide a default value for the `query` option in your DataTable type. This avoids having to recreate the query builder every time you create the DataTable.

Similar to how Symfony forms allow initializing `data` when no data is provided, you can initialize the `query` option.

To do this, add the `query` option to your DataTable type:

```php
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\ProductRepository;

class ProductDataTableType extends AbstractDataTableType 
{
    public function __construct(
        private ProductRepository $productRepository,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'query' => $this->productRepository->createQueryBuilder('p'),
        ]);
    }
}
```

This allows you to define a default configuration that is fully overridable.

When a default value is defined, it is no longer mandatory to pass the query as the second argument of the `createDataTable` method.

```php
$dataTable = $this->createDataTable(ProductDataTableType::class);
```

## Overriding the option

When you define a default `query`, you can still override it when creating the data table in the controller:

```php
$dataTable = $this->createDataTable(ProductDataTableType::class, $customQuery);
```

Or by passing it in the options:

```php
$dataTable = $this->createDataTable(ProductDataTableType::class, null, [
    'query' => $customQuery,
]);
```

### Extending the default query

If you want to reuse the default query defined in the `ProductDataTableType` and add a condition from the controller, you can use the `OptionsResolver` normalizer:

```php
use App\DataTable\Type\ProductDataTableType;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\OptionsResolver\Options;
use Doctrine\ORM\QueryBuilder;

class ProductController extends AbstractController
{
    use DataTableFactoryAwareTrait;
    
    public function index()
    {
        $dataTable = $this->createDataTable(ProductDataTableType::class, null, [
            'query' => function (Options $options, $query) {
                if ($query instanceof QueryBuilder) {
                    $query->andWhere('p.active = :active')
                          ->setParameter('active', true);
                }

                return $query;
            },
        ]);
    }
}
```
