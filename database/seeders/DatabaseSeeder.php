<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Property;
use App\Models\SliderSlide;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Администратор',
            'email' => 'admin@gallery.local',
            'password' => 'password',
            'role' => 'admin',
            'phone' => '+7 (999) 000-00-01',
        ]);

        $users = [
            ['name' => 'Александр Иванов', 'email' => 'alex@example.com', 'phone' => '+7 (999) 111-22-33'],
            ['name' => 'Екатерина Смирнова', 'email' => 'ekaterina@example.com', 'phone' => '+7 (999) 222-33-44'],
            ['name' => 'Михаил Громов', 'email' => 'mikhail@example.com', 'phone' => '+7 (999) 333-44-55'],
        ];

        $createdUsers = collect($users)->map(fn (array $data) => User::create([
            ...$data,
            'password' => 'password',
            'role' => 'user',
        ]));

        $properties = [
            [
                'title' => 'Резиденция «Акант»',
                'address' => 'Санкт-Петербург, Невский пр.',
                'category' => 'apartment',
                'type' => '4-комнатная',
                'area' => 180,
                'rooms' => 4,
                'floor' => 12,
                'price' => 89000000,
                'bonus' => '✦ Бонус: 0.7% токенами + дизайн-проект',
                'image_url' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=600&q=80',
                'badge' => 'VIP',
                'status' => 'for_sale',
                'sort_order' => 1,
            ],
            [
                'title' => 'Пентхаус «Золотая линия»',
                'address' => 'Москва, Тверская ул.',
                'category' => 'apartment',
                'type' => '5-комнатная',
                'area' => 267,
                'rooms' => 5,
                'floor' => 22,
                'price' => 149000000,
                'bonus' => '✦ Ипотека 1% + отделка премиум-класса',
                'image_url' => 'https://images.unsplash.com/photo-1600566752355-35792bedcfea?w=600&q=80',
                'badge' => 'VIP',
                'status' => 'for_sale',
                'sort_order' => 2,
            ],
            [
                'title' => 'Лофт «Архитектон»',
                'address' => 'Санкт-Петербург, наб. реки Мойки',
                'category' => 'apartment',
                'type' => '2-комнатная',
                'area' => 112,
                'rooms' => 2,
                'floor' => 5,
                'price' => 42500000,
                'bonus' => '✦ Отделка + парковочное место в подарок',
                'image_url' => 'https://images.unsplash.com/photo-1600585152915-d208bec867a1?w=600&q=80',
                'badge' => 'VIP',
                'status' => 'for_sale',
                'sort_order' => 3,
            ],
            [
                'title' => 'Апартаменты «Небо»',
                'address' => 'Москва, Кутузовский пр.',
                'category' => 'apartment',
                'type' => '2-комнатная',
                'area' => 95,
                'rooms' => 2,
                'floor' => 15,
                'price' => 37800000,
                'bonus' => '✦ Крипто-кешбэк 0.5% + консьерж-сервис',
                'image_url' => 'https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea?w=600&q=80',
                'badge' => 'VIP',
                'status' => 'for_sale',
                'sort_order' => 4,
            ],
            [
                'title' => 'Резиденция «Матовый горизонт»',
                'address' => 'Санкт-Петербург, Крестовский',
                'category' => 'apartment',
                'type' => '4-комнатная',
                'area' => 215,
                'rooms' => 4,
                'floor' => 3,
                'price' => 124500000,
                'bonus' => '✦ Хоумстейджинг + управление арендой 2 года',
                'image_url' => 'https://images.unsplash.com/photo-1600607688969-a5bfcd646154?w=600&q=80',
                'badge' => 'VIP',
                'status' => 'for_sale',
                'sort_order' => 5,
            ],
            [
                'title' => 'Вилла Forest House',
                'address' => 'Московская обл., Рублёвка',
                'category' => 'purchase',
                'type' => '6-комнатная',
                'area' => 420,
                'rooms' => 6,
                'floor' => 2,
                'price' => 275000000,
                'bonus' => '✦ Управление арендой 2 года',
                'image_url' => 'https://images.unsplash.com/photo-1600585154526-990dced4db0d?w=600&q=80',
                'badge' => 'VIP',
                'status' => 'for_sale',
                'sort_order' => 6,
            ],
            [
                'title' => 'Апартаменты «Панорама»',
                'address' => 'Москва, Пресненская наб.',
                'category' => 'rent',
                'type' => '2-комнатная',
                'area' => 88,
                'rooms' => 2,
                'floor' => 18,
                'price' => 450000,
                'bonus' => '✦ Коммунальные услуги включены',
                'image_url' => 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?w=600&q=80',
                'badge' => 'VIP',
                'status' => 'for_sale',
                'sort_order' => 7,
            ],
            [
                'title' => 'Коттедж «Сосновый бор»',
                'address' => 'Ленинградская обл., Vartiosaari',
                'category' => 'izhs',
                'type' => '5-комнатная',
                'area' => 310,
                'rooms' => 5,
                'floor' => 2,
                'price' => 68000000,
                'bonus' => '✦ Проект + строительство под ключ',
                'image_url' => 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=600&q=80',
                'badge' => 'VIP',
                'status' => 'for_sale',
                'sort_order' => 8,
            ],
            [
                'title' => 'Ремонт «Матовый минимализм»',
                'address' => 'Санкт-Петербург, Петроградская',
                'category' => 'renovation',
                'type' => '3-комнатная',
                'area' => 120,
                'rooms' => 3,
                'floor' => 7,
                'price' => 8500000,
                'bonus' => '✦ Дизайн-проект в подарок',
                'image_url' => 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=600&q=80',
                'badge' => 'VIP',
                'status' => 'for_sale',
                'sort_order' => 9,
            ],
            [
                'title' => 'Хоумстейджинг «Белый лофт»',
                'address' => 'Москва, Хамовники',
                'category' => 'home_staging',
                'type' => '2-комнатная',
                'area' => 75,
                'rooms' => 2,
                'floor' => 4,
                'price' => 320000,
                'bonus' => '✦ Фотосессия + 3D-тур включены',
                'image_url' => 'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?w=600&q=80',
                'badge' => 'VIP',
                'status' => 'for_sale',
                'sort_order' => 10,
            ],
        ];

        $createdProperties = collect($properties)->map(fn (array $data) => Property::create($data));

        $slides = [
            [
                'title' => 'Жилой комплекс «Матовый горизонт»',
                'subtitle' => 'Современная архитектура',
                'image_url' => 'https://images.unsplash.com/photo-1600585154526-990dced4db0d?w=1600&q=80',
                'sort_order' => 1,
            ],
            [
                'title' => 'Дизайн-проект «Золотое сечение»',
                'subtitle' => 'Интерьер премиум',
                'image_url' => 'https://images.unsplash.com/photo-1600566752355-35792bedcfea?w=1600&q=80',
                'sort_order' => 2,
            ],
            [
                'title' => 'Вилла в стиле архитектурной галереи',
                'subtitle' => 'Вилла с бассейном',
                'image_url' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=1600&q=80',
                'sort_order' => 3,
            ],
            [
                'title' => 'Панорамное остекление, премиум класс',
                'subtitle' => 'Современный особняк',
                'image_url' => 'https://images.unsplash.com/photo-1600607688969-a5bfcd646154?w=1600&q=80',
                'sort_order' => 4,
            ],
        ];

        foreach ($slides as $slide) {
            SliderSlide::create($slide);
        }

        Order::create([
            'number' => 'GSP-001',
            'user_id' => $createdUsers[0]->id,
            'property_id' => $createdProperties[0]->id,
            'status' => 'active',
            'amount' => $createdProperties[0]->price,
            'bonus' => $createdProperties[0]->bonus,
            'message' => 'Интересует просмотр объекта',
        ]);

        Order::create([
            'number' => 'GSP-002',
            'user_id' => $createdUsers[1]->id,
            'property_id' => $createdProperties[5]->id,
            'status' => 'pending',
            'amount' => $createdProperties[5]->price,
            'bonus' => $createdProperties[5]->bonus,
        ]);

        Order::create([
            'number' => 'GSP-003',
            'user_id' => $createdUsers[2]->id,
            'property_id' => $createdProperties[1]->id,
            'status' => 'completed',
            'amount' => $createdProperties[1]->price,
            'bonus' => $createdProperties[1]->bonus,
        ]);

        Order::create([
            'number' => 'GSP-004',
            'user_id' => $createdUsers[0]->id,
            'property_id' => null,
            'status' => 'pending',
            'bonus' => 'Бесплатная консультация для 3-х членов семьи или друзей при заключении договора',
            'amount' => 0,
            'message' => 'Активация промо-бонуса с главной страницы',
        ]);
    }
}
