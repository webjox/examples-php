<?php

namespace app\models\api;

use OpenApi\Annotations\Options;
use Stem\LinguaStemRu;
use Yii;
use yii\db\Query;

/**
 * This is the model class for table "product".
 *
 * @property int $id
 * @property string $name
 * @property int|null $is_service
 * @property int|null $id_category
 * @property string|null $image_url
 * @property string|null $video_url
 * @property string|null $attributes
 * @property string|null $description
 * @property int|null $is_available
 * @property float|null $price
 * @property float|null $price_wholesale
 * @property float|null $price_stock
 * @property int|null $id_manufacturer
 *
 * @property Category $category
 * @property Manufacturer $manufacturer
 * @property OrderProduct[] $orderProducts
 * @property ProductEffect[] $productEffects
 * @property ProductFiles[] $productFiles
 * @property ProductImages[] $productImages
 * @property ProductOptions[] $productOptions
 * @property ProductTag[] $productTags
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['is_service', 'id_category', 'is_available', 'id_manufacturer'], 'integer'],
            [['price', 'price_wholesale', 'price_stock'], 'number'],
            [['name'], 'string', 'max' => 200],
            [['image_url'], 'file', 'extensions' => 'png, jpg, jpeg, webp'],
            [['video_url', 'attributes'], 'string', 'max' => 300],
            [['description'], 'string', 'max' => 1000],
            [['id_category'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['id_category' => 'id']],
            [['id_manufacturer'], 'exist', 'skipOnError' => true, 'targetClass' => Manufacturer::className(), 'targetAttribute' => ['id_manufacturer' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'is_service' => 'Услуга',
            'id_category' => 'Категория',
            'image_url' => 'Картинка',
            'video_url' => 'Ссылка на видео',
            'attributes' => 'Артикул',
            'description' => 'Описание',
            'is_available' => 'Доступно',
            'price' => 'Цена',
            'price_wholesale' => 'Цена для оптовика',
            'price_stock' => 'Цена со склада',
            'id_manufacturer' => 'Производитель',
        ];
    }

    public function extraFields()
    {
        return [
            'category' => function (Product $model) {
                return Category::find()->where(['id' => $model->id_category])->one();
            },
            'manufacturer' => function (Product $model) {
                return Manufacturer::find()->where(['id' => $model->id_manufacturer])->one();
            },
            'options' => function (Product $model) {
                return ProductOptions::find()->where(['id_product' => $model->id])->all();
            },
            'effects' => function (Product $model) {
                return ProductEffect::find()->where(['id_product' => $model->id])->all();
            },
            'files' => function (Product $model) {
                return ProductFiles::find()->where(['id_product' => $model->id])->all();
            },
            'images' => function (Product $model) {
                return ProductImages::find()->where(['id_product' => $model->id])->all();
            },
            'shops' => function (Product $model) {
                return ProductShop::find()->where(['id_product' => $model->id])->all();
            },
            'tags' => function (Product $model) {
                return ProductTag::find()->where(['id_product' => $model->id])->all();
            },
        ];
    }

    public static function SearchProduct($search){
        $temp = explode(' ', $search);
        $words = [];
        $stemmer = new LinguaStemRu();
        foreach ($temp as $item) {
            if (iconv_strlen($item) > 3) {
                $words[] = $stemmer->stem_word($item);
            } else {
                $words[] = $item;
            }
        }
        $relevance = "IF (`name` LIKE '%" . $words[0] . "%', 2, 0)";
        $relevance .= " + IF (`attributes` LIKE '%" . $words[0] . "%', 1, 0)";
        for ($i = 0; $i < count($words); $i++) {
            $relevance .= " + IF (`name` LIKE '%" . $words[$i] . "%', 2, 0)";
            $relevance .= " + IF (`attributes` LIKE '%" . $words[$i] . "%', 1, 0)";
            $query = (new Query())
                ->select(['*', 'relevance' => $relevance])
                ->from('product');
            for ($i = 0; $i < count($words); $i++) {
                $query = $query->orWhere(['like', 'name', $words[$i]]);
                $query = $query->orWhere(['like', 'attributes', $words[$i]]);
            }
            $query = $query->orderBy(['relevance' => SORT_DESC]);
            return $query->all();
        }
    }


    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public
    function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'id_category']);
    }

    /**
     * Gets query for [[Manufacturer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public
    function getManufacturer()
    {
        return $this->hasOne(Manufacturer::className(), ['id' => 'id_manufacturer']);
    }

    /**
     * Gets query for [[OrderProducts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public
    function getOrderProducts()
    {
        return $this->hasMany(OrderProduct::className(), ['id_product' => 'id']);
    }

    /**
     * Gets query for [[ProductEffects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public
    function getProductEffects()
    {
        return $this->hasMany(ProductEffect::className(), ['id_product' => 'id']);
    }

    /**
     * Gets query for [[ProductFiles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public
    function getProductFiles()
    {
        return $this->hasMany(ProductFiles::className(), ['id_product' => 'id']);
    }

    /**
     * Gets query for [[ProductImages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public
    function getProductImages()
    {
        return $this->hasMany(ProductImages::className(), ['id_product' => 'id']);
    }

    /**
     * Gets query for [[ProductOptions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public
    function getProductOptions()
    {
        return $this->hasMany(ProductOptions::className(), ['id_product' => 'id']);
    }

    /**
     * Gets query for [[ProductTags]].
     *
     * @return \yii\db\ActiveQuery
     */
    public
    function getProductTags()
    {
        return $this->hasMany(ProductTag::className(), ['id_product' => 'id']);
    }
}
