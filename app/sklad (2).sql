-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Хост: db
-- Время создания: Дек 17 2025 г., 21:22
-- Версия сервера: 8.0.41
-- Версия PHP: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `sklad`
--

-- --------------------------------------------------------

--
-- Структура таблицы `client`
--

CREATE TABLE `client` (
  `client_id` smallint NOT NULL,
  `client_email` varchar(1024) DEFAULT NULL,
  `client_phone` varchar(20) NOT NULL,
  `client_address` varchar(1024) NOT NULL,
  `client_company_or_full_name` varchar(1024) NOT NULL,
  `is_deleted` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `client`
--

INSERT INTO `client` (`client_id`, `client_email`, `client_phone`, `client_address`, `client_company_or_full_name`, `is_deleted`) VALUES
(1, 'john.doe@company.com', '1234', '123 Main St, Cityville', 'John Doe Enterprises', 1),
(2, 'jane.smith@business.net', '5678', '456 Oak Ave, Townsville', 'Jane Smith Co.', 0),
(3, 'bob.jones@trade.org', '9012', '789 Pine Rd, Villagetown', 'Bob Jones Trading', 0),
(4, 'pipinisbegger@gmail.com', '915', 'Лупкина Губкина 44', 'ООО Дон строй', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `employee`
--

CREATE TABLE `employee` (
  `employee_id` smallint NOT NULL,
  `employee_phone` varchar(20) NOT NULL,
  `employee_full_name` varchar(200) NOT NULL,
  `shipment_number` smallint DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `employee`
--

INSERT INTO `employee` (`employee_id`, `employee_phone`, `employee_full_name`, `shipment_number`, `is_deleted`) VALUES
(1, '2345', 'Петров Василий Пупкович', 1, 0),
(2, '6789', 'Мария Петрова', 2, 0),
(3, '3456', 'Алексей Сидоров', 3, 0),
(4, '2266', 'Никита Никита Никита', NULL, 0),
(5, '89155593321', 'вовоовво', NULL, 0);

--
-- Триггеры `employee`
--
DELIMITER $$
CREATE TRIGGER `update_shipping_employee_full_name` AFTER UPDATE ON `employee` FOR EACH ROW BEGIN
    IF OLD.employee_full_name != NEW.employee_full_name THEN
        UPDATE shipping
        SET employee_full_name = NEW.employee_full_name
        WHERE employee_id = NEW.employee_id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Структура таблицы `equipment_instance`
--

CREATE TABLE `equipment_instance` (
  `equipment_instance_code` smallint NOT NULL,
  `employee_id` smallint DEFAULT NULL,
  `product_code` smallint NOT NULL,
  `equipment_instance_price` decimal(8,2) NOT NULL,
  `equipment_instance_name` varchar(1024) NOT NULL,
  `equipment_instance_status` varchar(1024) NOT NULL,
  `employee_full_name` varchar(200) NOT NULL,
  `is_deleted` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `equipment_instance`
--

INSERT INTO `equipment_instance` (`equipment_instance_code`, `employee_id`, `product_code`, `equipment_instance_price`, `equipment_instance_name`, `equipment_instance_status`, `employee_full_name`, `is_deleted`) VALUES
(1, 1, 1, 1000.00, 'Болгарка 4', 'Operational', 'Петров Василий Пупкович', 0),
(2, 2, 2, 260.00, 'Фреза коническая 300мм', 'In Maintenance', '', 0),
(3, 3, 3, 150.00, 'Шуруповерт аккумуляторный №13', 'Operational', '', 1),
(8, 2, 1, 1000.00, 'Ручка шариковая', 'Operational', 'Мария Петрова', 0),
(9, 2, 1, 2000.00, 'Ручка гелевая', 'Operational', 'Мария Петрова', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `maintenance`
--

CREATE TABLE `maintenance` (
  `equipment_instance_code` smallint NOT NULL,
  `maintenance_number` smallint NOT NULL,
  `master_id` smallint NOT NULL,
  `maintenance_price` decimal(8,2) NOT NULL,
  `maintenance_status` varchar(1024) NOT NULL,
  `maintenance_date` date NOT NULL,
  `master_full_name` varchar(100) DEFAULT NULL,
  `is_new` tinyint(1) DEFAULT '0',
  `is_deleted` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `maintenance`
--

INSERT INTO `maintenance` (`equipment_instance_code`, `maintenance_number`, `master_id`, `maintenance_price`, `maintenance_status`, `maintenance_date`, `master_full_name`, `is_new`, `is_deleted`) VALUES
(1, 5, 1, 4.00, 'Запланировано', '2025-06-22', 'Сергей Ковалёв', 0, 0),
(1, 11, 1, 11.00, 'Запланировано', '2025-06-22', NULL, 0, 0),
(1, 12, 3, 1.00, 'Запланировано', '2025-06-22', NULL, 1, 0),
(1, 13, 2, 1.00, 'Запланировано', '2025-06-22', NULL, 1, 0),
(1, 14, 1, 1000.00, 'Запланировано', '2025-08-08', NULL, 0, 0),
(1, 15, 3, 20.00, 'Запланировано', '2025-12-01', NULL, 0, 0),
(1, 18, 3, 300.00, 'Запланировано', '2025-12-01', NULL, 0, 0),
(1, 19, 5, 300.00, 'Запланировано', '2025-12-01', NULL, 0, 0),
(1, 20, 1, 300.00, 'Запланировано', '2025-12-01', NULL, 0, 0),
(2, 13, 1, 100.00, 'Запланировано', '2025-06-24', NULL, 0, 0),
(8, 33, 5, 222.00, 'В процессе', '2025-08-08', NULL, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `master`
--

CREATE TABLE `master` (
  `master_id` smallint NOT NULL,
  `master_phone` varchar(20) NOT NULL,
  `master_full_name` varchar(100) NOT NULL,
  `maintenance_number` smallint DEFAULT NULL,
  `user_id` int NOT NULL,
  `is_deleted` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `master`
--

INSERT INTO `master` (`master_id`, `master_phone`, `master_full_name`, `maintenance_number`, `user_id`, `is_deleted`) VALUES
(1, '1111', 'Сергей Ковалёв', NULL, 4, 0),
(2, '2222', 'Елена Смирнова', NULL, 0, 0),
(3, '3333', 'Дмитрий Попов', NULL, 0, 0),
(5, '578787878787', 'ljkkhnljknh kljnj', NULL, 1, 0);

--
-- Триггеры `master`
--
DELIMITER $$
CREATE TRIGGER `update_maintenance_master_full_name` AFTER UPDATE ON `master` FOR EACH ROW BEGIN
    IF OLD.master_full_name != NEW.master_full_name THEN
        UPDATE maintenance
        SET master_full_name = NEW.master_full_name
        WHERE master_id = NEW.master_id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Структура таблицы `product`
--

CREATE TABLE `product` (
  `product_name` varchar(1024) NOT NULL,
  `product_code` smallint NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `stock_quantity` smallint NOT NULL,
  `product_unit_of_measurement` varchar(1024) NOT NULL,
  `is_deleted` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `product`
--

INSERT INTO `product` (`product_name`, `product_code`, `price`, `stock_quantity`, `product_unit_of_measurement`, `is_deleted`) VALUES
('ТФ300х200', 1, 999.99, 50, 'unit', 0),
('ПФРК500', 2, 249.99, 30, 'unit', 0),
('Теплица 4х15', 3, 149.99, 100, 'unit', 0),
('Кувалда', 4, 200.00, 1, 'кг', 0),
('Ручка шариковая', 5, 48.00, 7, 'шт', 0),
('Карандаш', 6, 15.00, 4, 'шт', 0),
('Туалетная бумага', 7, 33.00, 150, 'шт', 0),
('ПФРК 100х200', 8, 1900.00, 87, 'шт', 0),
('Формировочная смесь', 9, 15.00, 3000, 'кг', 0),
('ТФ 80х80', 10, 200.00, 15, 'шт', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `product_instance`
--

CREATE TABLE `product_instance` (
  `product_code` smallint NOT NULL,
  `supply_number` smallint NOT NULL,
  `equipment_instance_code` smallint NOT NULL,
  `product_quantity` smallint NOT NULL,
  `product_instance_price` decimal(8,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `product_instance`
--

INSERT INTO `product_instance` (`product_code`, `supply_number`, `equipment_instance_code`, `product_quantity`, `product_instance_price`) VALUES
(1, 1, 1, 10, 950.00),
(2, 2, 2, 5, 230.00),
(3, 3, 3, 20, 140.00);

-- --------------------------------------------------------

--
-- Структура таблицы `shipment_product`
--

CREATE TABLE `shipment_product` (
  `shipment_number` smallint NOT NULL,
  `product_code` smallint NOT NULL,
  `shipment_product_quantity` smallint NOT NULL,
  `shipment_product_price` decimal(10,2) NOT NULL,
  `is_deleted` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `shipment_product`
--

INSERT INTO `shipment_product` (`shipment_number`, `product_code`, `shipment_product_quantity`, `shipment_product_price`, `is_deleted`) VALUES
(1, 1, 5, 4800.00, 0),
(2, 2, 3, 720.00, 0),
(3, 3, 10, 1450.00, 0),
(5, 1, 1, 100000.00, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `shipping`
--

CREATE TABLE `shipping` (
  `shipping_date` date NOT NULL,
  `shipment_status` varchar(1024) NOT NULL,
  `shipment_number` smallint NOT NULL,
  `employee_id` smallint NOT NULL,
  `client_id` smallint NOT NULL,
  `employee_full_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `is_new` tinyint DEFAULT '1',
  `is_deleted` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `shipping`
--

INSERT INTO `shipping` (`shipping_date`, `shipment_status`, `shipment_number`, `employee_id`, `client_id`, `employee_full_name`, `is_new`, `is_deleted`) VALUES
('2025-06-01', 'Shipped', 1, 1, 1, 'Петров Василий Пупкович', 0, 0),
('2025-06-05', 'Pending', 2, 2, 2, 'Мария Петрова', 1, 0),
('2025-06-10', 'Delivered', 3, 3, 3, 'Алексей Сидоров', 1, 0),
('2025-08-08', 'Delivered', 5, 1, 2, NULL, 0, 0),
('2025-06-26', 'В обработке', 6, 3, 3, NULL, 1, 1),
('2025-07-08', 'В обработке', 7, 4, 4, NULL, 1, 0),
('2025-10-10', 'Доставлено', 8, 4, 2, NULL, 1, 0),
('2025-09-09', 'В обработке', 12, 1, 3, NULL, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `supplier`
--

CREATE TABLE `supplier` (
  `supplier_address` varchar(1024) NOT NULL,
  `supplier_id` smallint NOT NULL,
  `supplier_phone` varchar(20) NOT NULL,
  `supplier_email` varchar(1024) DEFAULT NULL,
  `supplier_company_or_full_name` varchar(1024) NOT NULL,
  `supplier_full_name` varchar(200) NOT NULL,
  `is_deleted` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `supplier`
--

INSERT INTO `supplier` (`supplier_address`, `supplier_id`, `supplier_phone`, `supplier_email`, `supplier_company_or_full_name`, `supplier_full_name`, `is_deleted`) VALUES
('101 Industrial Rd, Supplytown', 1, '4444', 'supply1@vendor.com', 'Tech Supplies Inc.', '', 0),
('202 Factory St, Warecity', 2, '5555', 'supply2@vendor.net', 'Global Parts Ltd.', '', 0),
('303 Commerce Ave, Tradecity', 3, '6666', 'supply3@trade.org', 'Quality Materials Co.', '', 0),
('чарчвапр', 4, '8888888888', 'or.nikita2004@gmail.com', 'варпчва', 'Nikita', 0),
('Замятина 4', 6, '8888888888', 'or.nikita2004@gmail.com', 'Романов Генадий', 'Романов Генадий', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `supply`
--

CREATE TABLE `supply` (
  `supply_status` varchar(1024) NOT NULL,
  `supply_number` smallint NOT NULL,
  `supplier_id` smallint NOT NULL,
  `supplier_company_or_full_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `is_deleted` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `supply`
--

INSERT INTO `supply` (`supply_status`, `supply_number`, `supplier_id`, `supplier_company_or_full_name`, `is_deleted`) VALUES
('Delivered', 1, 1, 'Педро Петрович', 0),
('In Transit', 2, 2, '', 0),
('Pending', 3, 3, '', 0),
('Delivered', 4, 1, 'Tech Supplies Inc.', 0),
('Delivered', 6, 6, 'Романов Генадий', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','accountant','employee','master') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `employee_id` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`, `created_at`, `employee_id`) VALUES
(1, 'admin', '$2y$10$5F4AGtcOTTiSwlOyZ/PZEe7G5pUx0s3W/EOUdMgB1./n65T0NQYuK', 'admin', '2025-06-13 08:06:55', ''),
(2, 'buh1', '$2y$10$68Ouqgc/X2/IQWS3TIF8ReYbgWfpPlw54yaKO8JgRQ8aYl1EoqAbC', 'accountant', '2025-06-13 08:17:40', ''),
(3, 'sotrudnik1', '$2y$10$XW10azOdAh.HS3ylVISDEOQ5OkOHkN0xcb6aoH7Harrduj/duHRVu', 'employee', '2025-06-13 08:18:02', '1'),
(4, 'master1', '$2y$10$rvlx6zlWIRnA4mZ7dh3t3etJnkX.qSOSyiTWKfCQVQdOCpAuO7Dze', 'master', '2025-06-13 08:18:16', '');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`client_id`);

--
-- Индексы таблицы `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`employee_id`),
  ADD KEY `FK_EMPLOYEE_HAS_SHIPMENT` (`shipment_number`);

--
-- Индексы таблицы `equipment_instance`
--
ALTER TABLE `equipment_instance`
  ADD PRIMARY KEY (`equipment_instance_code`),
  ADD KEY `FK_EQUIPMEN_INCLUDES_PRODUCT_` (`product_code`),
  ADD KEY `FK_EQUIPMEN_IS_ASSIGN_EMPLOYEE` (`employee_id`);

--
-- Индексы таблицы `maintenance`
--
ALTER TABLE `maintenance`
  ADD PRIMARY KEY (`equipment_instance_code`,`maintenance_number`),
  ADD KEY `FK_MAINTENA_CONDUCTS_MASTER` (`master_id`),
  ADD KEY `idx_maintenance_number` (`maintenance_number`);

--
-- Индексы таблицы `master`
--
ALTER TABLE `master`
  ADD PRIMARY KEY (`master_id`);

--
-- Индексы таблицы `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_code`);

--
-- Индексы таблицы `product_instance`
--
ALTER TABLE `product_instance`
  ADD PRIMARY KEY (`product_code`),
  ADD KEY `FK_PRODUCT__CONTAINS_SUPPLY` (`supply_number`);

--
-- Индексы таблицы `shipment_product`
--
ALTER TABLE `shipment_product`
  ADD PRIMARY KEY (`shipment_number`,`product_code`),
  ADD KEY `FK_SHIPMENT_IS_REDIRE_PRODUCT` (`product_code`);

--
-- Индексы таблицы `shipping`
--
ALTER TABLE `shipping`
  ADD PRIMARY KEY (`shipment_number`),
  ADD KEY `FK_SHIPPING_IS_EXECUT_EMPLOYEE` (`employee_id`),
  ADD KEY `FK_SHIPPING_IS_SENT_CLIENT` (`client_id`);

--
-- Индексы таблицы `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`supplier_id`);

--
-- Индексы таблицы `supply`
--
ALTER TABLE `supply`
  ADD PRIMARY KEY (`supply_number`),
  ADD KEY `FK_SUPPLY_PERFORMS_SUPPLIER` (`supplier_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `employee`
--
ALTER TABLE `employee`
  ADD CONSTRAINT `FK_EMPLOYEE_HAS_SHIPMENT` FOREIGN KEY (`shipment_number`) REFERENCES `shipping` (`shipment_number`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `equipment_instance`
--
ALTER TABLE `equipment_instance`
  ADD CONSTRAINT `FK_EQUIPMEN_INCLUDES_PRODUCT_` FOREIGN KEY (`product_code`) REFERENCES `product_instance` (`product_code`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_EQUIPMEN_IS_ASSIGN_EMPLOYEE` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Ограничения внешнего ключа таблицы `maintenance`
--
ALTER TABLE `maintenance`
  ADD CONSTRAINT `FK_MAINTENA_CONDUCTS_MASTER` FOREIGN KEY (`master_id`) REFERENCES `master` (`master_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_MAINTENA_PASSES_EQUIPMEN` FOREIGN KEY (`equipment_instance_code`) REFERENCES `equipment_instance` (`equipment_instance_code`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Ограничения внешнего ключа таблицы `product_instance`
--
ALTER TABLE `product_instance`
  ADD CONSTRAINT `FK_PRODUCT__CONTAINS_SUPPLY` FOREIGN KEY (`supply_number`) REFERENCES `supply` (`supply_number`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_PRODUCT__HAS_PRODUCT` FOREIGN KEY (`product_code`) REFERENCES `product` (`product_code`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Ограничения внешнего ключа таблицы `shipment_product`
--
ALTER TABLE `shipment_product`
  ADD CONSTRAINT `FK_SHIPMENT_IS_DIRECT_SHIPPING` FOREIGN KEY (`shipment_number`) REFERENCES `shipping` (`shipment_number`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_SHIPMENT_IS_REDIRE_PRODUCT` FOREIGN KEY (`product_code`) REFERENCES `product` (`product_code`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Ограничения внешнего ключа таблицы `shipping`
--
ALTER TABLE `shipping`
  ADD CONSTRAINT `FK_SHIPPING_IS_EXECUT_EMPLOYEE` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_SHIPPING_IS_SENT_CLIENT` FOREIGN KEY (`client_id`) REFERENCES `client` (`client_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Ограничения внешнего ключа таблицы `supply`
--
ALTER TABLE `supply`
  ADD CONSTRAINT `FK_SUPPLY_PERFORMS_SUPPLIER` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`supplier_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
