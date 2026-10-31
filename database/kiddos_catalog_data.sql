-- =====================================================================
-- Kiddo's - full catalog data for the team database (kiddos.sql, Oct 31)
-- ONE file for all our data. Run it on the `kiddos` database AFTER kiddos.sql:
--   phpMyAdmin -> select `kiddos` -> Import -> this file -> Go
-- Safe to run more than once (it removes its own rows first).
--
-- Part A - adds 94 products (ids 101-200) with 2 photos each, 4 product
--          discounts and 16 reviews. Team rows are not changed.
-- Part B - fixes missing team data:
--   1) real photos for the 35 team products (ids 20-54) whose image files
--      did not exist (e.g. 'teddy_bear.jpg'), two photos each
--   2) category pictures (were empty): uploads/categories/<slug>.jpg
--   3) Toddlers / Little Kids age group pictures (pointed to missing files)
--   4) users.profile_image column (used by auth/profile.php, missing in dump)
-- Images: assets/images/products/<category-slug>/<product>-1.jpg and -2.jpg
-- Categories used: 20 Educational | 21 Wooden | 22 Plush & Soft | 23 Active Play
-- Photos: Unsplash (free license).
-- =====================================================================

START TRANSACTION;

-- ===================== PART A: our products =====================
-- Remove our own rows from a previous run. Their images, reviews and
-- discounts are removed too (ON DELETE CASCADE in the team schema).
DELETE FROM `products` WHERE `id` BETWEEN 101 AND 200;

-- ---------- 1) Products ----------
INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `instructions`, `price`, `stock`, `gender`, `min_age`, `max_age`, `image`, `is_active`, `created_at`, `updated_at`) VALUES
(101, 20, 'Rainbow Wooden Abacus', 'Classic 10-row wooden abacus with bright beads that makes counting, adding and subtracting easy to see and touch.', 'Slide the beads from left to right while counting out loud. Wipe clean with a dry cloth.', 9.50, 40, 'unisex', 3, 7, 'assets/images/products/educational-toys/rainbow-wooden-abacus-1.jpg', 1, '2026-10-01 09:13:00', '2026-10-01 09:13:00'),
(102, 20, 'ABC Letter Blocks (28 pcs)', 'Solid wooden cubes printed with letters, numbers and pictures for first spelling, stacking and sorting games.', 'Build simple words together and say each letter as you stack it. Not for children under 18 months.', 12.00, 45, 'unisex', 2, 5, 'assets/images/products/educational-toys/abc-letter-blocks-28-pcs-1.jpg', 1, '2026-10-01 16:26:00', '2026-10-01 16:26:00'),
(103, 20, 'Foam Alphabet & Number Shapes', 'Soft, colourful foam letters and numbers that are safe for little hands and perfect for spelling first words.', 'Use on a table or in the bath. Rinse and air dry after water play.', 6.50, 60, 'unisex', 3, 6, 'assets/images/products/educational-toys/foam-alphabet-number-shapes-1.jpg', 1, '2026-10-01 23:39:00', '2026-10-01 23:39:00'),
(104, 20, 'Montessori Number Puzzle Board', 'Chunky wooden numbers 0-9 with a rainbow counting wheel that builds number recognition and fine motor skills.', 'Take all pieces out, then match each number to its place. Count the rainbow sections together.', 11.00, 35, 'unisex', 2, 5, 'assets/images/products/educational-toys/montessori-number-puzzle-board-1.jpg', 1, '2026-10-02 06:52:00', '2026-10-02 06:52:00'),
(105, 20, 'Junior Explorer Microscope', 'Real-glass kids microscope with LED light and prepared slides, so young scientists can explore leaves, hair and salt crystals.', 'Start with the lowest zoom. Adult supervision required with glass slides.', 32.00, 15, 'unisex', 6, 12, 'assets/images/products/educational-toys/junior-explorer-microscope-1.jpg', 1, '2026-10-02 13:05:00', '2026-10-02 13:05:00'),
(106, 20, 'Young Astronomer Telescope', 'Easy-to-use refractor telescope with tripod and two eyepieces for viewing the moon and distant landscapes.', 'Never look at the sun. Set the tripod on a flat surface and focus slowly.', 45.00, 10, 'unisex', 6, 12, 'assets/images/products/educational-toys/young-astronomer-telescope-1.jpg', 1, '2026-10-02 20:18:00', '2026-10-02 20:18:00'),
(107, 20, 'Classic Desk World Globe', 'Colourful 25 cm world globe on a sturdy stand that turns geography lessons into a spin-and-discover game.', 'Spin the globe, stop it with a finger and find the country together.', 18.50, 25, 'unisex', 6, 12, 'assets/images/products/educational-toys/classic-desk-world-globe-1.jpg', 1, '2026-10-03 03:31:00', '2026-10-03 03:31:00'),
(108, 20, 'Little Scientist Lab Kit', 'Safe chemistry set with test tubes, goggles and 20 colourful experiments explained in a picture booklet.', 'Adult supervision required. Wear the goggles and follow the booklet step by step.', 24.00, 20, 'unisex', 7, 12, 'assets/images/products/educational-toys/little-scientist-lab-kit-1.jpg', 1, '2026-10-03 10:44:00', '2026-10-03 10:44:00'),
(109, 20, 'Speed Cube 3x3', 'Smooth-turning 3x3 puzzle cube that trains memory, patience and spatial thinking.', 'Learn the beginner method one layer at a time. Do not twist with force.', 4.50, 80, 'unisex', 7, 12, 'assets/images/products/educational-toys/speed-cube-3x3-1.jpg', 1, '2026-10-03 17:57:00', '2026-10-03 17:57:00'),
(110, 20, 'Wooden Chess Set for Kids', 'Hand-finished wooden chess set with a folding board and a beginner rules card.', 'Learn how each piece moves first, then play short practice games.', 15.00, 30, 'unisex', 6, 12, 'assets/images/products/educational-toys/wooden-chess-set-for-kids-1.jpg', 1, '2026-10-04 00:10:00', '2026-10-04 00:10:00'),
(111, 20, 'Rainbow Stacking Rings', 'Soft-edged stacking rings in bright colours that teach size order, colours and hand-eye coordination.', 'Show your child how to stack the rings from biggest to smallest.', 6.00, 55, 'unisex', 1, 3, 'assets/images/products/educational-toys/rainbow-stacking-rings-1.jpg', 1, '2026-10-04 07:23:00', '2026-10-04 07:23:00'),
(112, 20, 'Wooden Shape Sorter Board', 'Colourful wooden shapes that fit onto matching pegs to learn shapes, colours and counting.', 'Name each shape and colour while your child plays.', 8.75, 40, 'unisex', 1, 3, 'assets/images/products/educational-toys/wooden-shape-sorter-board-1.jpg', 1, '2026-10-04 14:36:00', '2026-10-04 14:36:00'),
(113, 20, 'Activity Cube Learning Center', 'Wooden activity cube with bead maze, sliding shapes and counting beads on every side.', 'Adult supervision required. Wipe clean with a dry cloth.', 27.00, 15, 'unisex', 1, 4, 'assets/images/products/educational-toys/activity-cube-learning-center-1.jpg', 1, '2026-10-04 21:49:00', '2026-10-04 21:49:00'),
(114, 20, 'Solar System Planets Model', 'Desktop solar system model with all eight planets that helps kids learn their order and size.', 'Name the planets from the sun outwards. Keep small parts away from toddlers.', 19.00, 20, 'unisex', 6, 12, 'assets/images/products/educational-toys/solar-system-planets-model-1.jpg', 1, '2026-10-05 04:02:00', '2026-10-05 04:02:00'),
(115, 20, 'Dinosaur Fossil Dig Kit', 'Dig out hidden dinosaur skeletons with a chisel and brush, just like a real paleontologist.', 'Dig on a tray or outdoors. Brush away the plaster gently to reveal the fossil.', 13.50, 30, 'boys', 6, 12, 'assets/images/products/educational-toys/dinosaur-fossil-dig-kit-1.jpg', 1, '2026-10-05 11:15:00', '2026-10-05 11:15:00'),
(116, 20, 'Little Doctor Play Kit', 'Pretend doctor kit with stethoscope, thermometer and bandages for caring for teddy patients.', 'Pretend play only. Do not put items in the mouth or ears.', 13.00, 30, 'unisex', 3, 7, 'assets/images/products/educational-toys/little-doctor-play-kit-1.jpg', 1, '2026-10-05 18:28:00', '2026-10-05 18:28:00'),
(117, 20, 'Watercolor Painting Set', 'Washable watercolour palette with brushes and a paper pad for colourful creative afternoons.', 'Cover the table before painting. Rinse brushes after use.', 10.50, 35, 'girls', 4, 10, 'assets/images/products/educational-toys/watercolor-painting-set-1.jpg', 1, '2026-10-06 01:41:00', '2026-10-06 01:41:00'),
(118, 20, 'Kids Jigsaw Puzzle (100 pcs)', 'Thick-card jigsaw puzzle with large colourful pieces that builds patience and problem-solving skills.', 'Start with the edge pieces, then fill the middle.', 5.50, 50, 'unisex', 5, 10, 'assets/images/products/educational-toys/kids-jigsaw-puzzle-100-pcs-1.jpg', 1, '2026-10-06 08:54:00', '2026-10-06 08:54:00'),
(119, 20, 'Nature Explorer Magnifying Glass', 'Shatter-resistant magnifying glass with a comfortable handle for exploring bugs, leaves and stones.', 'Never use it to look at the sun or to focus sunlight.', 5.00, 60, 'unisex', 4, 10, 'assets/images/products/educational-toys/nature-explorer-magnifying-glass-1.jpg', 1, '2026-10-06 15:07:00', '2026-10-06 15:07:00'),
(120, 20, 'Color Sorting Bowls Set', 'Rainbow wooden bowls with matching pieces for sorting, counting and colour recognition.', 'Ask your child to put each piece in the bowl of the same colour.', 9.00, 40, 'unisex', 2, 5, 'assets/images/products/educational-toys/color-sorting-bowls-set-1.jpg', 1, '2026-10-06 22:20:00', '2026-10-06 22:20:00'),
(121, 21, 'Classic Wooden Train Set', 'Wooden locomotive with wagons and track pieces that connect with magnets.', 'Build the track on the floor and connect the wagons with the magnets.', 29.00, 20, 'boys', 3, 7, 'assets/images/products/wooden-toys/classic-wooden-train-set-1.jpg', 1, '2026-10-01 10:33:00', '2026-10-01 10:33:00'),
(122, 21, 'Wooden Rainbow Stacker', 'Seven-piece wooden rainbow for stacking, building and open-ended imaginative play.', 'Stack, balance or line up the arches. Wipe with a damp cloth.', 16.00, 30, 'unisex', 1, 4, 'assets/images/products/wooden-toys/wooden-rainbow-stacker-1.jpg', 1, '2026-10-01 17:46:00', '2026-10-01 17:46:00'),
(123, 21, 'Pastel Wooden Stacking Tower', 'Smooth wooden rings in soft pastel shades on a stable base, made from sustainable wood.', 'Stack the rings by size. Check pieces regularly for damage.', 10.50, 35, 'unisex', 1, 3, 'assets/images/products/wooden-toys/pastel-wooden-stacking-tower-1.jpg', 1, '2026-10-02 00:59:00', '2026-10-02 00:59:00'),
(124, 21, 'Three-Story Wooden Dollhouse', 'Large wooden dollhouse with balconies, stairs and open rooms ready to decorate.', 'Adult assembly required. Wipe with a dry cloth.', 75.00, 8, 'girls', 3, 9, 'assets/images/products/wooden-toys/three-story-wooden-dollhouse-1.jpg', 1, '2026-10-02 07:12:00', '2026-10-02 07:12:00'),
(125, 21, 'Wooden Play Kitchen', 'Wooden play kitchen with oven, sink, shelves and hooks for pretend cooking adventures.', 'Adult assembly required. Keep away from damp areas.', 95.00, 6, 'girls', 3, 7, 'assets/images/products/wooden-toys/wooden-play-kitchen-1.jpg', 1, '2026-10-02 14:25:00', '2026-10-02 14:25:00'),
(126, 21, 'Vintage Rocking Horse', 'Sturdy wooden rocking horse with smooth runners and comfortable handles.', 'Use on a flat floor. Adult supervision required for toddlers.', 58.00, 6, 'unisex', 1, 4, 'assets/images/products/wooden-toys/vintage-rocking-horse-1.jpg', 1, '2026-10-02 21:38:00', '2026-10-02 21:38:00'),
(127, 21, 'Colorful Wooden Building Blocks', 'Painted wooden blocks in many shapes and colours for towers, bridges and houses.', 'Build on a flat floor. Put blocks back in the box after play.', 21.00, 30, 'unisex', 2, 6, 'assets/images/products/wooden-toys/colorful-wooden-building-blocks-1.jpg', 1, '2026-10-03 04:51:00', '2026-10-03 04:51:00'),
(128, 21, 'Natural Wooden Cube Set (50 pcs)', 'Unpainted beech wood cubes that build balance, planning and creativity.', 'Wipe with a dry cloth. Do not soak in water.', 14.00, 30, 'unisex', 3, 8, 'assets/images/products/wooden-toys/natural-wooden-cube-set-50-pcs-1.jpg', 1, '2026-10-03 11:04:00', '2026-10-03 11:04:00'),
(129, 21, 'Rainbow Wooden Xylophone', 'Bright eight-note xylophone with two mallets for first melodies.', 'Use the mallets only. Play simple songs together.', 9.00, 40, 'unisex', 1, 5, 'assets/images/products/wooden-toys/rainbow-wooden-xylophone-1.jpg', 1, '2026-10-03 18:17:00', '2026-10-03 18:17:00'),
(130, 21, 'Wooden Pull-Along Animals', 'Cheerful wooden animals on wheels that follow little walkers on a string.', 'For walking children only. Supervise play with the string.', 8.50, 30, 'unisex', 1, 3, 'assets/images/products/wooden-toys/wooden-pull-along-animals-1.jpg', 1, '2026-10-04 01:30:00', '2026-10-04 01:30:00'),
(131, 21, 'Wooden Bead Maze Roller Coaster', 'Classic bead maze with colourful wires that trains hand-eye coordination.', 'Slide the beads along the wires. Check the base is stable before play.', 15.50, 25, 'unisex', 1, 4, 'assets/images/products/wooden-toys/wooden-bead-maze-roller-coaster-1.jpg', 1, '2026-10-04 08:43:00', '2026-10-04 08:43:00'),
(132, 21, 'Wooden Tool Box Set', 'Wooden tool box with hammer, saw, screwdriver and nuts for little builders.', 'Pretend tools only. Keep small nuts away from younger children.', 19.00, 20, 'boys', 3, 7, 'assets/images/products/wooden-toys/wooden-tool-box-set-1.jpg', 1, '2026-10-04 15:56:00', '2026-10-04 15:56:00'),
(133, 21, 'Wooden Toy Cars Collection', 'Smooth wooden cars with rolling wheels for races, garages and road trips.', 'Roll on flat floors. Wipe clean with a dry cloth.', 12.50, 35, 'boys', 2, 6, 'assets/images/products/wooden-toys/wooden-toy-cars-collection-1.jpg', 1, '2026-10-04 22:09:00', '2026-10-04 22:09:00'),
(134, 21, 'Wooden Biplane', 'Bright yellow wooden biplane with spinning propeller and rolling wheels.', 'Spin the propeller gently. Not suitable for outdoor wet play.', 11.00, 25, 'boys', 3, 8, 'assets/images/products/wooden-toys/wooden-biplane-1.jpg', 1, '2026-10-05 05:22:00', '2026-10-05 05:22:00'),
(135, 21, 'Wooden Food Cutting Set', 'Wooden fruits and vegetables joined with velcro that kids slice with a wooden knife.', 'Wipe with a damp cloth. Do not soak in water.', 10.50, 35, 'girls', 2, 6, 'assets/images/products/wooden-toys/wooden-food-cutting-set-1.jpg', 1, '2026-10-05 12:35:00', '2026-10-05 12:35:00'),
(136, 21, 'Wooden Doll Pram', 'Sturdy doll pram that little ones push around the house while caring for their dolls.', 'Use on flat floors only, away from stairs.', 34.00, 10, 'girls', 2, 5, 'assets/images/products/wooden-toys/wooden-doll-pram-1.jpg', 1, '2026-10-05 19:48:00', '2026-10-05 19:48:00'),
(137, 21, 'Wooden Spinning Tops Set', 'Hand-painted wooden spinning tops that spin for a long time on smooth surfaces.', 'Twist the handle quickly and let go on a flat table.', 6.00, 50, 'unisex', 4, 10, 'assets/images/products/wooden-toys/wooden-spinning-tops-set-1.jpg', 1, '2026-10-06 02:01:00', '2026-10-06 02:01:00'),
(138, 21, 'Wooden Sailboat Model', 'Classic wooden sailboat with cotton sails that floats in pools and ponds.', 'Float only under adult supervision. Dry after use.', 17.50, 15, 'boys', 4, 10, 'assets/images/products/wooden-toys/wooden-sailboat-model-1.jpg', 1, '2026-10-06 09:14:00', '2026-10-06 09:14:00'),
(139, 21, 'Wooden Brain Teaser Puzzle', 'Interlocking wooden puzzle that challenges kids to take it apart and rebuild it.', 'Take it apart slowly and remember the order of the pieces.', 5.50, 45, 'unisex', 7, 12, 'assets/images/products/wooden-toys/wooden-brain-teaser-puzzle-1.jpg', 1, '2026-10-06 16:27:00', '2026-10-06 16:27:00'),
(140, 21, 'Wooden Alphabet Number Cubes', 'Engraved wooden cubes with letters, numbers and pictures for learning through play.', 'Stack the cubes and read the letters together.', 10.00, 40, 'unisex', 2, 5, 'assets/images/products/wooden-toys/wooden-alphabet-number-cubes-1.jpg', 1, '2026-10-06 23:40:00', '2026-10-06 23:40:00'),
(141, 22, 'Porcelain Princess Doll', 'Elegant collectible doll with a lace dress, bonnet and long soft hair.', 'Handle with care. Keep away from younger siblings.', 28.00, 15, 'girls', 5, 12, 'assets/images/products/girls-toys/porcelain-princess-doll-1.jpg', 1, '2026-10-01 11:53:00', '2026-10-01 11:53:00'),
(142, 22, 'Vintage Classic Doll', 'Classic doll with blinking eyes, curly hair and a pretty tartan dress.', 'Comb the hair gently. Surface wash only.', 22.00, 15, 'girls', 4, 10, 'assets/images/products/girls-toys/vintage-classic-doll-1.jpg', 1, '2026-10-01 18:06:00', '2026-10-01 18:06:00'),
(143, 22, 'Knitted Rag Doll', 'Soft hand-knitted rag doll with colourful yarn hair, perfect for cuddles.', 'Hand wash in cold water. Air dry.', 12.00, 25, 'girls', 1, 6, 'assets/images/products/girls-toys/knitted-rag-doll-1.jpg', 1, '2026-10-02 01:19:00', '2026-10-02 01:19:00'),
(144, 22, 'Fluffy Unicorn Plush', 'Super-soft unicorn plush with a golden horn and a rainbow mane.', 'Surface wash only. Air dry.', 14.50, 30, 'girls', 1, 8, 'assets/images/products/girls-toys/fluffy-unicorn-plush-1.jpg', 1, '2026-10-02 08:32:00', '2026-10-02 08:32:00'),
(145, 22, 'Sparkle Eyes Unicorn Plush', 'Pink unicorn plush with big sparkly eyes and glitter horn.', 'Surface wash only. Not for children under 12 months.', 11.00, 30, 'girls', 2, 8, 'assets/images/products/girls-toys/sparkle-eyes-unicorn-plush-1.jpg', 1, '2026-10-02 15:45:00', '2026-10-02 15:45:00'),
(146, 22, 'Soft Bunny Plush', 'Floppy-eared bunny plush made from extra-soft fabric, safe for little ones.', 'Machine wash on gentle cycle in a laundry bag.', 10.00, 35, 'girls', 1, 6, 'assets/images/products/girls-toys/soft-bunny-plush-1.jpg', 1, '2026-10-02 22:58:00', '2026-10-02 22:58:00'),
(147, 22, 'Love Teddy Bear Gift', 'Soft teddy bear holding a red heart, a sweet gift for birthdays and special days.', 'Surface wash only. Air dry.', 13.00, 25, 'girls', 1, 10, 'assets/images/products/girls-toys/love-teddy-bear-gift-1.jpg', 1, '2026-10-03 05:11:00', '2026-10-03 05:11:00'),
(150, 20, 'Bead Jewelry Making Kit', 'Over 500 colourful beads and letter beads to design bracelets and necklaces.', 'Small parts: not for children under 3. Sort beads in the case after play.', 9.50, 40, 'girls', 6, 12, 'assets/images/products/girls-toys/bead-jewelry-making-kit-1.jpg', 1, '2026-10-04 02:50:00', '2026-10-04 02:50:00'),
(151, 20, 'Friendship Bracelet Kit', 'Colourful threads and a step-by-step guide to weave friendship bracelets.', 'Follow the guide patterns from easy to hard.', 7.00, 40, 'girls', 6, 12, 'assets/images/products/girls-toys/friendship-bracelet-kit-1.jpg', 1, '2026-10-04 09:03:00', '2026-10-04 09:03:00'),
(153, 23, 'Teepee Play Tent', 'Cosy cotton teepee with wooden poles and a window for reading corners and sleepovers.', 'Adult assembly required. Spot clean only.', 39.00, 10, 'girls', 3, 9, 'assets/images/products/girls-toys/teepee-play-tent-1.jpg', 1, '2026-10-04 23:29:00', '2026-10-04 23:29:00'),
(157, 20, 'Rainbow Modeling Clay Kit', 'Soft, non-toxic modeling clay in rainbow colours with shaping tools.', 'Close the tubs tightly after play so the clay stays soft. Do not eat.', 7.50, 45, 'girls', 3, 8, 'assets/images/products/girls-toys/rainbow-modeling-clay-kit-1.jpg', 1, '2026-10-06 03:21:00', '2026-10-06 03:21:00'),
(158, 20, 'Mini Grand Piano', 'Elegant mini grand piano with real-sounding keys and a matching stool.', 'Requires 2 AA batteries. Keep the volume low for small ears.', 26.00, 12, 'girls', 3, 8, 'assets/images/products/girls-toys/mini-grand-piano-1.jpg', 1, '2026-10-06 10:34:00', '2026-10-06 10:34:00'),
(159, 20, 'Jumbo Crayons Coloring Set', 'Bright jumbo crayons that are easy to hold, for drawing and colouring adventures.', 'Use on paper only. Not for children under 3.', 5.00, 60, 'girls', 3, 8, 'assets/images/products/girls-toys/jumbo-crayons-coloring-set-1.jpg', 1, '2026-10-06 17:47:00', '2026-10-06 17:47:00'),
(160, 22, 'Cuddly Kitty Plush', 'Fluffy purple kitty plush with soft fur and embroidered face.', 'Surface wash only. Air dry.', 9.50, 30, 'girls', 1, 8, 'assets/images/products/girls-toys/cuddly-kitty-plush-1.jpg', 1, '2026-10-07 00:00:00', '2026-10-07 00:00:00'),
(161, 23, 'Kids Balance Bike', 'Lightweight pedal-free balance bike that helps toddlers learn steering and balance.', 'Adjust the seat so both feet touch the ground. Always wear a helmet.', 45.00, 15, 'unisex', 2, 5, 'assets/images/products/outdoor-sports/kids-balance-bike-1.jpg', 1, '2026-10-01 12:13:00', '2026-10-01 12:13:00'),
(162, 23, 'Pink Balance Bike', 'Pedal-free balance bike in a girls'' colour with soft grips and puncture-proof tyres.', 'Adjust the seat so both feet touch the ground. Always wear a helmet.', 45.00, 12, 'girls', 2, 5, 'assets/images/products/outdoor-sports/pink-balance-bike-1.jpg', 1, '2026-10-01 19:26:00', '2026-10-01 19:26:00'),
(163, 23, '16-inch Blue Kids Bicycle', 'Steel-frame kids bicycle with removable training wheels and front hand brake.', 'Check tyre pressure and brakes before every ride. Always wear a helmet.', 89.00, 10, 'boys', 4, 8, 'assets/images/products/outdoor-sports/16-inch-blue-kids-bicycle-1.jpg', 1, '2026-10-02 02:39:00', '2026-10-02 02:39:00'),
(164, 23, '16-inch Pink Kids Bicycle', 'Pretty kids bicycle with training wheels, chain guard and front basket.', 'Check tyre pressure and brakes before every ride. Always wear a helmet.', 89.00, 10, 'girls', 4, 8, 'assets/images/products/outdoor-sports/16-inch-pink-kids-bicycle-1.jpg', 1, '2026-10-02 09:52:00', '2026-10-02 09:52:00'),
(165, 23, 'Foldable Kick Scooter', 'Height-adjustable scooter with smooth wheels and rear foot brake.', 'Ride on flat, dry surfaces only. Wear a helmet and knee pads.', 35.00, 20, 'unisex', 4, 10, 'assets/images/products/outdoor-sports/foldable-kick-scooter-1.jpg', 1, '2026-10-02 16:05:00', '2026-10-02 16:05:00'),
(166, 23, 'Pink Kids Scooter', 'Pink scooter with light-up wheels and an easy-fold frame.', 'Ride on flat, dry surfaces only. Wear a helmet and knee pads.', 38.00, 15, 'girls', 5, 10, 'assets/images/products/outdoor-sports/pink-kids-scooter-1.jpg', 1, '2026-10-02 23:18:00', '2026-10-02 23:18:00'),
(167, 23, 'Kids Football Size 4', 'Soft-touch stitched football sized for young players.', 'Inflate to the pressure printed on the ball. Play on grass or turf.', 8.50, 60, 'unisex', 5, 12, 'assets/images/products/outdoor-sports/kids-football-size-4-1.jpg', 1, '2026-10-03 06:31:00', '2026-10-03 06:31:00'),
(168, 23, 'Adjustable Basketball Hoop', 'Free-standing hoop that grows with your child, with net and ball included.', 'Fill the base with sand or water before use. Do not hang on the rim.', 27.50, 18, 'unisex', 5, 12, 'assets/images/products/outdoor-sports/adjustable-basketball-hoop-1.jpg', 1, '2026-10-03 13:44:00', '2026-10-03 13:44:00'),
(169, 23, 'Garden Trampoline with Safety Net', 'Outdoor trampoline with padded frame and enclosure net for safe bouncing.', 'One child at a time. Adult supervision required.', 135.00, 5, 'unisex', 4, 12, 'assets/images/products/outdoor-sports/garden-trampoline-with-safety-net-1.jpg', 1, '2026-10-03 20:57:00', '2026-10-03 20:57:00'),
(170, 23, 'Toddler Swing Seat', 'Bucket swing seat with safety belt and adjustable ropes for trees or frames.', 'Check ropes before every use. Adult supervision required.', 24.00, 15, 'unisex', 1, 4, 'assets/images/products/outdoor-sports/toddler-swing-seat-1.jpg', 1, '2026-10-04 03:10:00', '2026-10-04 03:10:00'),
(171, 23, 'Kids Skateboard', 'Maple deck skateboard with grip tape and smooth wheels for beginners.', 'Always wear a helmet and pads. Ride away from traffic.', 29.00, 15, 'boys', 6, 12, 'assets/images/products/outdoor-sports/kids-skateboard-1.jpg', 1, '2026-10-04 10:23:00', '2026-10-04 10:23:00'),
(172, 23, 'Rainbow Roller Skates', 'Adjustable quad roller skates with comfy padding and rainbow details.', 'Wear a helmet, knee and wrist pads. Skate on smooth ground.', 32.00, 15, 'girls', 6, 12, 'assets/images/products/outdoor-sports/rainbow-roller-skates-1.jpg', 1, '2026-10-04 17:36:00', '2026-10-04 17:36:00'),
(173, 23, 'Rainbow Kite', 'Easy-to-fly kite with long colourful tails and 50 m string.', 'Fly in open spaces away from power lines.', 7.50, 40, 'unisex', 5, 12, 'assets/images/products/outdoor-sports/rainbow-kite-1.jpg', 1, '2026-10-05 00:49:00', '2026-10-05 00:49:00'),
(174, 23, 'Junior Badminton Set', 'Two lightweight rackets with shuttlecocks for backyard games.', 'Play in open areas. Replace damaged shuttlecocks.', 12.00, 30, 'unisex', 6, 12, 'assets/images/products/outdoor-sports/junior-badminton-set-1.jpg', 1, '2026-10-05 07:02:00', '2026-10-05 07:02:00'),
(175, 23, 'Kids Bike Helmet', 'Lightweight ventilated helmet with adjustable strap for bikes and scooters.', 'Fit snugly and fasten the strap before every ride.', 16.00, 40, 'unisex', 3, 10, 'assets/images/products/outdoor-sports/kids-bike-helmet-1.jpg', 1, '2026-10-05 14:15:00', '2026-10-05 14:15:00'),
(176, 23, 'Beach Sand Toys Set', 'Bucket, spade, rake and moulds for building sandcastles at the beach or sandbox.', 'Rinse with clean water after the beach.', 8.25, 40, 'unisex', 2, 6, 'assets/images/products/outdoor-sports/beach-sand-toys-set-1.jpg', 1, '2026-10-05 21:28:00', '2026-10-05 21:28:00'),
(177, 23, 'Water Blaster Pump Gun', 'Pump-action water blaster with long range and easy-fill tank.', 'Do not aim at faces. Use outdoors only.', 7.50, 50, 'boys', 5, 12, 'assets/images/products/outdoor-sports/water-blaster-pump-gun-1.jpg', 1, '2026-10-06 04:41:00', '2026-10-06 04:41:00'),
(178, 23, 'Junior Tennis Racket', 'Lightweight junior racket with a cover and two soft tennis balls.', 'Choose the right size for your child''s height.', 14.00, 25, 'unisex', 6, 12, 'assets/images/products/outdoor-sports/junior-tennis-racket-1.jpg', 1, '2026-10-06 11:54:00', '2026-10-06 11:54:00'),
(179, 23, 'Rainbow Hula Hoop', 'Colourful adjustable hula hoop for fitness, dance and playground games.', 'Use in open spaces away from furniture.', 5.50, 40, 'girls', 4, 12, 'assets/images/products/outdoor-sports/rainbow-hula-hoop-1.jpg', 1, '2026-10-06 18:07:00', '2026-10-06 18:07:00'),
(180, 23, 'Giant Bubble Wand Set', 'Bubble wands with non-toxic solution for giant bubbles in the garden.', 'Use outdoors. Do not drink the solution.', 6.00, 50, 'unisex', 3, 8, 'assets/images/products/outdoor-sports/giant-bubble-wand-set-1.jpg', 1, '2026-10-07 01:20:00', '2026-10-07 01:20:00'),
(181, 23, 'RC Off-Road Rock Crawler', 'Rugged 4x4 remote control crawler with shock absorbers and rechargeable battery.', 'Charge fully before first use. Do not drive through water.', 42.00, 15, 'boys', 6, 12, 'assets/images/products/boys-toys/rc-off-road-rock-crawler-1.jpg', 1, '2026-10-01 13:33:00', '2026-10-01 13:33:00'),
(182, 23, 'RC Sports Racing Car', 'Fast remote control sports car with 2.4 GHz remote and LED lights.', 'Charge fully before first use. Drive on smooth surfaces.', 38.00, 15, 'boys', 6, 12, 'assets/images/products/boys-toys/rc-sports-racing-car-1.jpg', 1, '2026-10-01 20:46:00', '2026-10-01 20:46:00'),
(183, 23, 'Monster Truck', 'Big-wheel monster truck with friction motor and strong suspension.', 'Push and release to drive. Wipe clean, do not submerge.', 16.00, 25, 'boys', 4, 10, 'assets/images/products/boys-toys/monster-truck-1.jpg', 1, '2026-10-02 03:59:00', '2026-10-02 03:59:00'),
(184, 23, 'Toy Excavator', 'Construction excavator with moving arm and digging bucket.', 'Play in sand or on the floor. Do not pull the arm too far.', 14.50, 25, 'boys', 3, 8, 'assets/images/products/boys-toys/toy-excavator-1.jpg', 1, '2026-10-02 10:12:00', '2026-10-02 10:12:00'),
(185, 23, 'Construction Wheel Loader', 'Heavy-duty toy wheel loader with lifting front scoop.', 'Wipe clean after sand play.', 16.50, 20, 'boys', 3, 8, 'assets/images/products/boys-toys/construction-wheel-loader-1.jpg', 1, '2026-10-02 17:25:00', '2026-10-02 17:25:00'),
(186, 23, 'Yellow Dump Truck', 'Chunky dump truck with tipping bed for hauling sand and blocks.', 'Wipe clean after outdoor play.', 12.00, 30, 'boys', 3, 7, 'assets/images/products/boys-toys/yellow-dump-truck-1.jpg', 1, '2026-10-03 00:38:00', '2026-10-03 00:38:00'),
(187, 20, 'Dinosaur Figures Set (12 pcs)', 'Detailed dinosaur figures in bright colours with name cards.', 'Use the cards to match each dinosaur to its name.', 15.00, 30, 'boys', 3, 9, 'assets/images/products/boys-toys/dinosaur-figures-set-12-pcs-1.jpg', 1, '2026-10-03 07:51:00', '2026-10-03 07:51:00'),
(188, 20, 'T-Rex Dinosaur Figure', 'Realistic T-Rex figure with textured skin and posable jaw.', 'Wipe clean with a damp cloth.', 9.50, 35, 'boys', 3, 10, 'assets/images/products/boys-toys/t-rex-dinosaur-figure-1.jpg', 1, '2026-10-03 14:04:00', '2026-10-03 14:04:00'),
(189, 20, 'Classic Tin Robot', 'Retro wind-up tin robot that walks and swings its arms.', 'Wind the key gently. Do not overwind.', 18.00, 15, 'boys', 5, 12, 'assets/images/products/boys-toys/classic-tin-robot-1.jpg', 1, '2026-10-03 21:17:00', '2026-10-03 21:17:00'),
(190, 20, 'Retro Robot Friends Set', 'Set of retro-style robots in bright colours for display and play.', 'Keep away from water. Wipe with a dry cloth.', 22.00, 12, 'boys', 5, 12, 'assets/images/products/boys-toys/retro-robot-friends-set-1.jpg', 1, '2026-10-04 04:30:00', '2026-10-04 04:30:00'),
(191, 23, 'Steam Train Railway Set', 'Battery-powered steam locomotive with wagons, track and station.', 'Requires 2 AA batteries. Build the track on a flat floor.', 48.00, 10, 'boys', 4, 10, 'assets/images/products/boys-toys/steam-train-railway-set-1.jpg', 1, '2026-10-04 11:43:00', '2026-10-04 11:43:00'),
(192, 23, 'Rescue Fire Truck', 'Rescue vehicle with siren sounds, lights and rolling wheels.', 'Requires 3 AAA batteries (included). Switch off after play.', 13.50, 25, 'boys', 3, 8, 'assets/images/products/boys-toys/rescue-fire-truck-1.jpg', 1, '2026-10-04 18:56:00', '2026-10-04 18:56:00'),
(193, 23, 'Red Biplane Toy', 'Classic red biplane with spinning propeller for high-flying adventures.', 'Spin the propeller gently. Keep away from water.', 11.50, 25, 'boys', 3, 9, 'assets/images/products/boys-toys/red-biplane-toy-1.jpg', 1, '2026-10-05 01:09:00', '2026-10-05 01:09:00'),
(194, 20, 'Building Bricks Set (500 pcs)', 'Compatible building bricks in many colours with an ideas booklet.', 'Small parts: not for children under 3. Sort bricks by colour.', 26.00, 25, 'boys', 5, 12, 'assets/images/products/boys-toys/building-bricks-set-500-pcs-1.jpg', 1, '2026-10-05 08:22:00', '2026-10-05 08:22:00'),
(195, 23, 'Racing Car Track Set', 'Flexible race track with curves, start line and two racing cars.', 'Build the track on the floor. Keep small parts away from toddlers.', 28.00, 20, 'boys', 4, 10, 'assets/images/products/boys-toys/racing-car-track-set-1.jpg', 1, '2026-10-05 15:35:00', '2026-10-05 15:35:00'),
(196, 20, 'Mini Camera Drone', 'Easy-to-fly mini drone with altitude hold, one-key take-off and HD camera.', 'Fly outdoors in calm weather. Adult supervision required.', 79.00, 8, 'boys', 10, 12, 'assets/images/products/boys-toys/mini-camera-drone-1.jpg', 1, '2026-10-05 22:48:00', '2026-10-05 22:48:00'),
(197, 21, 'Pirate Ship Model', 'Detailed wooden pirate ship model with sails and cannons.', 'Display piece. Keep away from water and dust regularly.', 35.00, 10, 'boys', 6, 12, 'assets/images/products/boys-toys/pirate-ship-model-1.jpg', 1, '2026-10-06 05:01:00', '2026-10-06 05:01:00'),
(198, 23, 'Police Patrol Car', 'Friendly police car with rolling wheels and rounded edges for little hands.', 'Wipe clean with a damp cloth.', 9.00, 35, 'boys', 2, 6, 'assets/images/products/boys-toys/police-patrol-car-1.jpg', 1, '2026-10-06 12:14:00', '2026-10-06 12:14:00'),
(199, 23, 'Die-Cast Cars Collection', 'Metal toy cars with free-rolling wheels and classic designs.', 'Small parts may come loose: check cars regularly.', 12.00, 40, 'boys', 3, 10, 'assets/images/products/boys-toys/die-cast-cars-collection-1.jpg', 1, '2026-10-06 19:27:00', '2026-10-06 19:27:00'),
(200, 23, 'Yellow School Bus', 'Die-cast school bus with opening door and pull-back motor.', 'Pull back and release to drive.', 10.50, 30, 'boys', 3, 8, 'assets/images/products/boys-toys/yellow-school-bus-1.jpg', 1, '2026-10-07 02:40:00', '2026-10-07 02:40:00');

-- ---------- 2) Two images per product (same product, two views) ----------
INSERT INTO `images` (`product_id`, `image_path`, `alt_text`, `is_primary`, `sort_order`, `created_at`) VALUES
(101, 'assets/images/products/educational-toys/rainbow-wooden-abacus-1.jpg', 'Rainbow Wooden Abacus - main photo', 1, 0, '2026-10-01 09:13:00'),
(101, 'assets/images/products/educational-toys/rainbow-wooden-abacus-2.jpg', 'Rainbow Wooden Abacus - second photo', 0, 1, '2026-10-01 09:13:00'),
(102, 'assets/images/products/educational-toys/abc-letter-blocks-28-pcs-1.jpg', 'ABC Letter Blocks (28 pcs) - main photo', 1, 0, '2026-10-01 16:26:00'),
(102, 'assets/images/products/educational-toys/abc-letter-blocks-28-pcs-2.jpg', 'ABC Letter Blocks (28 pcs) - second photo', 0, 1, '2026-10-01 16:26:00'),
(103, 'assets/images/products/educational-toys/foam-alphabet-number-shapes-1.jpg', 'Foam Alphabet & Number Shapes - main photo', 1, 0, '2026-10-01 23:39:00'),
(103, 'assets/images/products/educational-toys/foam-alphabet-number-shapes-2.jpg', 'Foam Alphabet & Number Shapes - second photo', 0, 1, '2026-10-01 23:39:00'),
(104, 'assets/images/products/educational-toys/montessori-number-puzzle-board-1.jpg', 'Montessori Number Puzzle Board - main photo', 1, 0, '2026-10-02 06:52:00'),
(104, 'assets/images/products/educational-toys/montessori-number-puzzle-board-2.jpg', 'Montessori Number Puzzle Board - second photo', 0, 1, '2026-10-02 06:52:00'),
(105, 'assets/images/products/educational-toys/junior-explorer-microscope-1.jpg', 'Junior Explorer Microscope - main photo', 1, 0, '2026-10-02 13:05:00'),
(105, 'assets/images/products/educational-toys/junior-explorer-microscope-2.jpg', 'Junior Explorer Microscope - second photo', 0, 1, '2026-10-02 13:05:00'),
(106, 'assets/images/products/educational-toys/young-astronomer-telescope-1.jpg', 'Young Astronomer Telescope - main photo', 1, 0, '2026-10-02 20:18:00'),
(106, 'assets/images/products/educational-toys/young-astronomer-telescope-2.jpg', 'Young Astronomer Telescope - second photo', 0, 1, '2026-10-02 20:18:00'),
(107, 'assets/images/products/educational-toys/classic-desk-world-globe-1.jpg', 'Classic Desk World Globe - main photo', 1, 0, '2026-10-03 03:31:00'),
(107, 'assets/images/products/educational-toys/classic-desk-world-globe-2.jpg', 'Classic Desk World Globe - second photo', 0, 1, '2026-10-03 03:31:00'),
(108, 'assets/images/products/educational-toys/little-scientist-lab-kit-1.jpg', 'Little Scientist Lab Kit - main photo', 1, 0, '2026-10-03 10:44:00'),
(108, 'assets/images/products/educational-toys/little-scientist-lab-kit-2.jpg', 'Little Scientist Lab Kit - second photo', 0, 1, '2026-10-03 10:44:00'),
(109, 'assets/images/products/educational-toys/speed-cube-3x3-1.jpg', 'Speed Cube 3x3 - main photo', 1, 0, '2026-10-03 17:57:00'),
(109, 'assets/images/products/educational-toys/speed-cube-3x3-2.jpg', 'Speed Cube 3x3 - second photo', 0, 1, '2026-10-03 17:57:00'),
(110, 'assets/images/products/educational-toys/wooden-chess-set-for-kids-1.jpg', 'Wooden Chess Set for Kids - main photo', 1, 0, '2026-10-04 00:10:00'),
(110, 'assets/images/products/educational-toys/wooden-chess-set-for-kids-2.jpg', 'Wooden Chess Set for Kids - second photo', 0, 1, '2026-10-04 00:10:00'),
(111, 'assets/images/products/educational-toys/rainbow-stacking-rings-1.jpg', 'Rainbow Stacking Rings - main photo', 1, 0, '2026-10-04 07:23:00'),
(111, 'assets/images/products/educational-toys/rainbow-stacking-rings-2.jpg', 'Rainbow Stacking Rings - second photo', 0, 1, '2026-10-04 07:23:00'),
(112, 'assets/images/products/educational-toys/wooden-shape-sorter-board-1.jpg', 'Wooden Shape Sorter Board - main photo', 1, 0, '2026-10-04 14:36:00'),
(112, 'assets/images/products/educational-toys/wooden-shape-sorter-board-2.jpg', 'Wooden Shape Sorter Board - second photo', 0, 1, '2026-10-04 14:36:00'),
(113, 'assets/images/products/educational-toys/activity-cube-learning-center-1.jpg', 'Activity Cube Learning Center - main photo', 1, 0, '2026-10-04 21:49:00'),
(113, 'assets/images/products/educational-toys/activity-cube-learning-center-2.jpg', 'Activity Cube Learning Center - second photo', 0, 1, '2026-10-04 21:49:00'),
(114, 'assets/images/products/educational-toys/solar-system-planets-model-1.jpg', 'Solar System Planets Model - main photo', 1, 0, '2026-10-05 04:02:00'),
(114, 'assets/images/products/educational-toys/solar-system-planets-model-2.jpg', 'Solar System Planets Model - second photo', 0, 1, '2026-10-05 04:02:00'),
(115, 'assets/images/products/educational-toys/dinosaur-fossil-dig-kit-1.jpg', 'Dinosaur Fossil Dig Kit - main photo', 1, 0, '2026-10-05 11:15:00'),
(115, 'assets/images/products/educational-toys/dinosaur-fossil-dig-kit-2.jpg', 'Dinosaur Fossil Dig Kit - second photo', 0, 1, '2026-10-05 11:15:00'),
(116, 'assets/images/products/educational-toys/little-doctor-play-kit-1.jpg', 'Little Doctor Play Kit - main photo', 1, 0, '2026-10-05 18:28:00'),
(116, 'assets/images/products/educational-toys/little-doctor-play-kit-2.jpg', 'Little Doctor Play Kit - second photo', 0, 1, '2026-10-05 18:28:00'),
(117, 'assets/images/products/educational-toys/watercolor-painting-set-1.jpg', 'Watercolor Painting Set - main photo', 1, 0, '2026-10-06 01:41:00'),
(117, 'assets/images/products/educational-toys/watercolor-painting-set-2.jpg', 'Watercolor Painting Set - second photo', 0, 1, '2026-10-06 01:41:00'),
(118, 'assets/images/products/educational-toys/kids-jigsaw-puzzle-100-pcs-1.jpg', 'Kids Jigsaw Puzzle (100 pcs) - main photo', 1, 0, '2026-10-06 08:54:00'),
(118, 'assets/images/products/educational-toys/kids-jigsaw-puzzle-100-pcs-2.jpg', 'Kids Jigsaw Puzzle (100 pcs) - second photo', 0, 1, '2026-10-06 08:54:00'),
(119, 'assets/images/products/educational-toys/nature-explorer-magnifying-glass-1.jpg', 'Nature Explorer Magnifying Glass - main photo', 1, 0, '2026-10-06 15:07:00'),
(119, 'assets/images/products/educational-toys/nature-explorer-magnifying-glass-2.jpg', 'Nature Explorer Magnifying Glass - second photo', 0, 1, '2026-10-06 15:07:00'),
(120, 'assets/images/products/educational-toys/color-sorting-bowls-set-1.jpg', 'Color Sorting Bowls Set - main photo', 1, 0, '2026-10-06 22:20:00'),
(120, 'assets/images/products/educational-toys/color-sorting-bowls-set-2.jpg', 'Color Sorting Bowls Set - second photo', 0, 1, '2026-10-06 22:20:00'),
(121, 'assets/images/products/wooden-toys/classic-wooden-train-set-1.jpg', 'Classic Wooden Train Set - main photo', 1, 0, '2026-10-01 10:33:00'),
(121, 'assets/images/products/wooden-toys/classic-wooden-train-set-2.jpg', 'Classic Wooden Train Set - second photo', 0, 1, '2026-10-01 10:33:00'),
(122, 'assets/images/products/wooden-toys/wooden-rainbow-stacker-1.jpg', 'Wooden Rainbow Stacker - main photo', 1, 0, '2026-10-01 17:46:00'),
(122, 'assets/images/products/wooden-toys/wooden-rainbow-stacker-2.jpg', 'Wooden Rainbow Stacker - second photo', 0, 1, '2026-10-01 17:46:00'),
(123, 'assets/images/products/wooden-toys/pastel-wooden-stacking-tower-1.jpg', 'Pastel Wooden Stacking Tower - main photo', 1, 0, '2026-10-02 00:59:00'),
(123, 'assets/images/products/wooden-toys/pastel-wooden-stacking-tower-2.jpg', 'Pastel Wooden Stacking Tower - second photo', 0, 1, '2026-10-02 00:59:00'),
(124, 'assets/images/products/wooden-toys/three-story-wooden-dollhouse-1.jpg', 'Three-Story Wooden Dollhouse - main photo', 1, 0, '2026-10-02 07:12:00'),
(124, 'assets/images/products/wooden-toys/three-story-wooden-dollhouse-2.jpg', 'Three-Story Wooden Dollhouse - second photo', 0, 1, '2026-10-02 07:12:00'),
(125, 'assets/images/products/wooden-toys/wooden-play-kitchen-1.jpg', 'Wooden Play Kitchen - main photo', 1, 0, '2026-10-02 14:25:00'),
(125, 'assets/images/products/wooden-toys/wooden-play-kitchen-2.jpg', 'Wooden Play Kitchen - second photo', 0, 1, '2026-10-02 14:25:00'),
(126, 'assets/images/products/wooden-toys/vintage-rocking-horse-1.jpg', 'Vintage Rocking Horse - main photo', 1, 0, '2026-10-02 21:38:00'),
(126, 'assets/images/products/wooden-toys/vintage-rocking-horse-2.jpg', 'Vintage Rocking Horse - second photo', 0, 1, '2026-10-02 21:38:00'),
(127, 'assets/images/products/wooden-toys/colorful-wooden-building-blocks-1.jpg', 'Colorful Wooden Building Blocks - main photo', 1, 0, '2026-10-03 04:51:00'),
(127, 'assets/images/products/wooden-toys/colorful-wooden-building-blocks-2.jpg', 'Colorful Wooden Building Blocks - second photo', 0, 1, '2026-10-03 04:51:00'),
(128, 'assets/images/products/wooden-toys/natural-wooden-cube-set-50-pcs-1.jpg', 'Natural Wooden Cube Set (50 pcs) - main photo', 1, 0, '2026-10-03 11:04:00'),
(128, 'assets/images/products/wooden-toys/natural-wooden-cube-set-50-pcs-2.jpg', 'Natural Wooden Cube Set (50 pcs) - second photo', 0, 1, '2026-10-03 11:04:00'),
(129, 'assets/images/products/wooden-toys/rainbow-wooden-xylophone-1.jpg', 'Rainbow Wooden Xylophone - main photo', 1, 0, '2026-10-03 18:17:00'),
(129, 'assets/images/products/wooden-toys/rainbow-wooden-xylophone-2.jpg', 'Rainbow Wooden Xylophone - second photo', 0, 1, '2026-10-03 18:17:00'),
(130, 'assets/images/products/wooden-toys/wooden-pull-along-animals-1.jpg', 'Wooden Pull-Along Animals - main photo', 1, 0, '2026-10-04 01:30:00'),
(130, 'assets/images/products/wooden-toys/wooden-pull-along-animals-2.jpg', 'Wooden Pull-Along Animals - second photo', 0, 1, '2026-10-04 01:30:00'),
(131, 'assets/images/products/wooden-toys/wooden-bead-maze-roller-coaster-1.jpg', 'Wooden Bead Maze Roller Coaster - main photo', 1, 0, '2026-10-04 08:43:00'),
(131, 'assets/images/products/wooden-toys/wooden-bead-maze-roller-coaster-2.jpg', 'Wooden Bead Maze Roller Coaster - second photo', 0, 1, '2026-10-04 08:43:00'),
(132, 'assets/images/products/wooden-toys/wooden-tool-box-set-1.jpg', 'Wooden Tool Box Set - main photo', 1, 0, '2026-10-04 15:56:00'),
(132, 'assets/images/products/wooden-toys/wooden-tool-box-set-2.jpg', 'Wooden Tool Box Set - second photo', 0, 1, '2026-10-04 15:56:00'),
(133, 'assets/images/products/wooden-toys/wooden-toy-cars-collection-1.jpg', 'Wooden Toy Cars Collection - main photo', 1, 0, '2026-10-04 22:09:00'),
(133, 'assets/images/products/wooden-toys/wooden-toy-cars-collection-2.jpg', 'Wooden Toy Cars Collection - second photo', 0, 1, '2026-10-04 22:09:00'),
(134, 'assets/images/products/wooden-toys/wooden-biplane-1.jpg', 'Wooden Biplane - main photo', 1, 0, '2026-10-05 05:22:00'),
(134, 'assets/images/products/wooden-toys/wooden-biplane-2.jpg', 'Wooden Biplane - second photo', 0, 1, '2026-10-05 05:22:00'),
(135, 'assets/images/products/wooden-toys/wooden-food-cutting-set-1.jpg', 'Wooden Food Cutting Set - main photo', 1, 0, '2026-10-05 12:35:00'),
(135, 'assets/images/products/wooden-toys/wooden-food-cutting-set-2.jpg', 'Wooden Food Cutting Set - second photo', 0, 1, '2026-10-05 12:35:00'),
(136, 'assets/images/products/wooden-toys/wooden-doll-pram-1.jpg', 'Wooden Doll Pram - main photo', 1, 0, '2026-10-05 19:48:00'),
(136, 'assets/images/products/wooden-toys/wooden-doll-pram-2.jpg', 'Wooden Doll Pram - second photo', 0, 1, '2026-10-05 19:48:00'),
(137, 'assets/images/products/wooden-toys/wooden-spinning-tops-set-1.jpg', 'Wooden Spinning Tops Set - main photo', 1, 0, '2026-10-06 02:01:00'),
(137, 'assets/images/products/wooden-toys/wooden-spinning-tops-set-2.jpg', 'Wooden Spinning Tops Set - second photo', 0, 1, '2026-10-06 02:01:00'),
(138, 'assets/images/products/wooden-toys/wooden-sailboat-model-1.jpg', 'Wooden Sailboat Model - main photo', 1, 0, '2026-10-06 09:14:00'),
(138, 'assets/images/products/wooden-toys/wooden-sailboat-model-2.jpg', 'Wooden Sailboat Model - second photo', 0, 1, '2026-10-06 09:14:00'),
(139, 'assets/images/products/wooden-toys/wooden-brain-teaser-puzzle-1.jpg', 'Wooden Brain Teaser Puzzle - main photo', 1, 0, '2026-10-06 16:27:00'),
(139, 'assets/images/products/wooden-toys/wooden-brain-teaser-puzzle-2.jpg', 'Wooden Brain Teaser Puzzle - second photo', 0, 1, '2026-10-06 16:27:00'),
(140, 'assets/images/products/wooden-toys/wooden-alphabet-number-cubes-1.jpg', 'Wooden Alphabet Number Cubes - main photo', 1, 0, '2026-10-06 23:40:00'),
(140, 'assets/images/products/wooden-toys/wooden-alphabet-number-cubes-2.jpg', 'Wooden Alphabet Number Cubes - second photo', 0, 1, '2026-10-06 23:40:00'),
(141, 'assets/images/products/girls-toys/porcelain-princess-doll-1.jpg', 'Porcelain Princess Doll - main photo', 1, 0, '2026-10-01 11:53:00'),
(141, 'assets/images/products/girls-toys/porcelain-princess-doll-2.jpg', 'Porcelain Princess Doll - second photo', 0, 1, '2026-10-01 11:53:00'),
(142, 'assets/images/products/girls-toys/vintage-classic-doll-1.jpg', 'Vintage Classic Doll - main photo', 1, 0, '2026-10-01 18:06:00'),
(142, 'assets/images/products/girls-toys/vintage-classic-doll-2.jpg', 'Vintage Classic Doll - second photo', 0, 1, '2026-10-01 18:06:00'),
(143, 'assets/images/products/girls-toys/knitted-rag-doll-1.jpg', 'Knitted Rag Doll - main photo', 1, 0, '2026-10-02 01:19:00'),
(143, 'assets/images/products/girls-toys/knitted-rag-doll-2.jpg', 'Knitted Rag Doll - second photo', 0, 1, '2026-10-02 01:19:00'),
(144, 'assets/images/products/girls-toys/fluffy-unicorn-plush-1.jpg', 'Fluffy Unicorn Plush - main photo', 1, 0, '2026-10-02 08:32:00'),
(144, 'assets/images/products/girls-toys/fluffy-unicorn-plush-2.jpg', 'Fluffy Unicorn Plush - second photo', 0, 1, '2026-10-02 08:32:00'),
(145, 'assets/images/products/girls-toys/sparkle-eyes-unicorn-plush-1.jpg', 'Sparkle Eyes Unicorn Plush - main photo', 1, 0, '2026-10-02 15:45:00'),
(145, 'assets/images/products/girls-toys/sparkle-eyes-unicorn-plush-2.jpg', 'Sparkle Eyes Unicorn Plush - second photo', 0, 1, '2026-10-02 15:45:00'),
(146, 'assets/images/products/girls-toys/soft-bunny-plush-1.jpg', 'Soft Bunny Plush - main photo', 1, 0, '2026-10-02 22:58:00'),
(146, 'assets/images/products/girls-toys/soft-bunny-plush-2.jpg', 'Soft Bunny Plush - second photo', 0, 1, '2026-10-02 22:58:00'),
(147, 'assets/images/products/girls-toys/love-teddy-bear-gift-1.jpg', 'Love Teddy Bear Gift - main photo', 1, 0, '2026-10-03 05:11:00'),
(147, 'assets/images/products/girls-toys/love-teddy-bear-gift-2.jpg', 'Love Teddy Bear Gift - second photo', 0, 1, '2026-10-03 05:11:00'),
(150, 'assets/images/products/girls-toys/bead-jewelry-making-kit-1.jpg', 'Bead Jewelry Making Kit - main photo', 1, 0, '2026-10-04 02:50:00'),
(150, 'assets/images/products/girls-toys/bead-jewelry-making-kit-2.jpg', 'Bead Jewelry Making Kit - second photo', 0, 1, '2026-10-04 02:50:00'),
(151, 'assets/images/products/girls-toys/friendship-bracelet-kit-1.jpg', 'Friendship Bracelet Kit - main photo', 1, 0, '2026-10-04 09:03:00'),
(151, 'assets/images/products/girls-toys/friendship-bracelet-kit-2.jpg', 'Friendship Bracelet Kit - second photo', 0, 1, '2026-10-04 09:03:00'),
(153, 'assets/images/products/girls-toys/teepee-play-tent-1.jpg', 'Teepee Play Tent - main photo', 1, 0, '2026-10-04 23:29:00'),
(153, 'assets/images/products/girls-toys/teepee-play-tent-2.jpg', 'Teepee Play Tent - second photo', 0, 1, '2026-10-04 23:29:00'),
(157, 'assets/images/products/girls-toys/rainbow-modeling-clay-kit-1.jpg', 'Rainbow Modeling Clay Kit - main photo', 1, 0, '2026-10-06 03:21:00'),
(157, 'assets/images/products/girls-toys/rainbow-modeling-clay-kit-2.jpg', 'Rainbow Modeling Clay Kit - second photo', 0, 1, '2026-10-06 03:21:00'),
(158, 'assets/images/products/girls-toys/mini-grand-piano-1.jpg', 'Mini Grand Piano - main photo', 1, 0, '2026-10-06 10:34:00'),
(158, 'assets/images/products/girls-toys/mini-grand-piano-2.jpg', 'Mini Grand Piano - second photo', 0, 1, '2026-10-06 10:34:00'),
(159, 'assets/images/products/girls-toys/jumbo-crayons-coloring-set-1.jpg', 'Jumbo Crayons Coloring Set - main photo', 1, 0, '2026-10-06 17:47:00'),
(159, 'assets/images/products/girls-toys/jumbo-crayons-coloring-set-2.jpg', 'Jumbo Crayons Coloring Set - second photo', 0, 1, '2026-10-06 17:47:00'),
(160, 'assets/images/products/girls-toys/cuddly-kitty-plush-1.jpg', 'Cuddly Kitty Plush - main photo', 1, 0, '2026-10-07 00:00:00'),
(160, 'assets/images/products/girls-toys/cuddly-kitty-plush-2.jpg', 'Cuddly Kitty Plush - second photo', 0, 1, '2026-10-07 00:00:00'),
(161, 'assets/images/products/outdoor-sports/kids-balance-bike-1.jpg', 'Kids Balance Bike - main photo', 1, 0, '2026-10-01 12:13:00'),
(161, 'assets/images/products/outdoor-sports/kids-balance-bike-2.jpg', 'Kids Balance Bike - second photo', 0, 1, '2026-10-01 12:13:00'),
(162, 'assets/images/products/outdoor-sports/pink-balance-bike-1.jpg', 'Pink Balance Bike - main photo', 1, 0, '2026-10-01 19:26:00'),
(162, 'assets/images/products/outdoor-sports/pink-balance-bike-2.jpg', 'Pink Balance Bike - second photo', 0, 1, '2026-10-01 19:26:00'),
(163, 'assets/images/products/outdoor-sports/16-inch-blue-kids-bicycle-1.jpg', '16-inch Blue Kids Bicycle - main photo', 1, 0, '2026-10-02 02:39:00'),
(163, 'assets/images/products/outdoor-sports/16-inch-blue-kids-bicycle-2.jpg', '16-inch Blue Kids Bicycle - second photo', 0, 1, '2026-10-02 02:39:00'),
(164, 'assets/images/products/outdoor-sports/16-inch-pink-kids-bicycle-1.jpg', '16-inch Pink Kids Bicycle - main photo', 1, 0, '2026-10-02 09:52:00'),
(164, 'assets/images/products/outdoor-sports/16-inch-pink-kids-bicycle-2.jpg', '16-inch Pink Kids Bicycle - second photo', 0, 1, '2026-10-02 09:52:00'),
(165, 'assets/images/products/outdoor-sports/foldable-kick-scooter-1.jpg', 'Foldable Kick Scooter - main photo', 1, 0, '2026-10-02 16:05:00'),
(165, 'assets/images/products/outdoor-sports/foldable-kick-scooter-2.jpg', 'Foldable Kick Scooter - second photo', 0, 1, '2026-10-02 16:05:00'),
(166, 'assets/images/products/outdoor-sports/pink-kids-scooter-1.jpg', 'Pink Kids Scooter - main photo', 1, 0, '2026-10-02 23:18:00'),
(166, 'assets/images/products/outdoor-sports/pink-kids-scooter-2.jpg', 'Pink Kids Scooter - second photo', 0, 1, '2026-10-02 23:18:00'),
(167, 'assets/images/products/outdoor-sports/kids-football-size-4-1.jpg', 'Kids Football Size 4 - main photo', 1, 0, '2026-10-03 06:31:00'),
(167, 'assets/images/products/outdoor-sports/kids-football-size-4-2.jpg', 'Kids Football Size 4 - second photo', 0, 1, '2026-10-03 06:31:00'),
(168, 'assets/images/products/outdoor-sports/adjustable-basketball-hoop-1.jpg', 'Adjustable Basketball Hoop - main photo', 1, 0, '2026-10-03 13:44:00'),
(168, 'assets/images/products/outdoor-sports/adjustable-basketball-hoop-2.jpg', 'Adjustable Basketball Hoop - second photo', 0, 1, '2026-10-03 13:44:00'),
(169, 'assets/images/products/outdoor-sports/garden-trampoline-with-safety-net-1.jpg', 'Garden Trampoline with Safety Net - main photo', 1, 0, '2026-10-03 20:57:00'),
(169, 'assets/images/products/outdoor-sports/garden-trampoline-with-safety-net-2.jpg', 'Garden Trampoline with Safety Net - second photo', 0, 1, '2026-10-03 20:57:00'),
(170, 'assets/images/products/outdoor-sports/toddler-swing-seat-1.jpg', 'Toddler Swing Seat - main photo', 1, 0, '2026-10-04 03:10:00'),
(170, 'assets/images/products/outdoor-sports/toddler-swing-seat-2.jpg', 'Toddler Swing Seat - second photo', 0, 1, '2026-10-04 03:10:00'),
(171, 'assets/images/products/outdoor-sports/kids-skateboard-1.jpg', 'Kids Skateboard - main photo', 1, 0, '2026-10-04 10:23:00'),
(171, 'assets/images/products/outdoor-sports/kids-skateboard-2.jpg', 'Kids Skateboard - second photo', 0, 1, '2026-10-04 10:23:00'),
(172, 'assets/images/products/outdoor-sports/rainbow-roller-skates-1.jpg', 'Rainbow Roller Skates - main photo', 1, 0, '2026-10-04 17:36:00'),
(172, 'assets/images/products/outdoor-sports/rainbow-roller-skates-2.jpg', 'Rainbow Roller Skates - second photo', 0, 1, '2026-10-04 17:36:00'),
(173, 'assets/images/products/outdoor-sports/rainbow-kite-1.jpg', 'Rainbow Kite - main photo', 1, 0, '2026-10-05 00:49:00'),
(173, 'assets/images/products/outdoor-sports/rainbow-kite-2.jpg', 'Rainbow Kite - second photo', 0, 1, '2026-10-05 00:49:00'),
(174, 'assets/images/products/outdoor-sports/junior-badminton-set-1.jpg', 'Junior Badminton Set - main photo', 1, 0, '2026-10-05 07:02:00'),
(174, 'assets/images/products/outdoor-sports/junior-badminton-set-2.jpg', 'Junior Badminton Set - second photo', 0, 1, '2026-10-05 07:02:00'),
(175, 'assets/images/products/outdoor-sports/kids-bike-helmet-1.jpg', 'Kids Bike Helmet - main photo', 1, 0, '2026-10-05 14:15:00'),
(175, 'assets/images/products/outdoor-sports/kids-bike-helmet-2.jpg', 'Kids Bike Helmet - second photo', 0, 1, '2026-10-05 14:15:00'),
(176, 'assets/images/products/outdoor-sports/beach-sand-toys-set-1.jpg', 'Beach Sand Toys Set - main photo', 1, 0, '2026-10-05 21:28:00'),
(176, 'assets/images/products/outdoor-sports/beach-sand-toys-set-2.jpg', 'Beach Sand Toys Set - second photo', 0, 1, '2026-10-05 21:28:00'),
(177, 'assets/images/products/outdoor-sports/water-blaster-pump-gun-1.jpg', 'Water Blaster Pump Gun - main photo', 1, 0, '2026-10-06 04:41:00'),
(177, 'assets/images/products/outdoor-sports/water-blaster-pump-gun-2.jpg', 'Water Blaster Pump Gun - second photo', 0, 1, '2026-10-06 04:41:00'),
(178, 'assets/images/products/outdoor-sports/junior-tennis-racket-1.jpg', 'Junior Tennis Racket - main photo', 1, 0, '2026-10-06 11:54:00'),
(178, 'assets/images/products/outdoor-sports/junior-tennis-racket-2.jpg', 'Junior Tennis Racket - second photo', 0, 1, '2026-10-06 11:54:00'),
(179, 'assets/images/products/outdoor-sports/rainbow-hula-hoop-1.jpg', 'Rainbow Hula Hoop - main photo', 1, 0, '2026-10-06 18:07:00'),
(179, 'assets/images/products/outdoor-sports/rainbow-hula-hoop-2.jpg', 'Rainbow Hula Hoop - second photo', 0, 1, '2026-10-06 18:07:00'),
(180, 'assets/images/products/outdoor-sports/giant-bubble-wand-set-1.jpg', 'Giant Bubble Wand Set - main photo', 1, 0, '2026-10-07 01:20:00'),
(180, 'assets/images/products/outdoor-sports/giant-bubble-wand-set-2.jpg', 'Giant Bubble Wand Set - second photo', 0, 1, '2026-10-07 01:20:00'),
(181, 'assets/images/products/boys-toys/rc-off-road-rock-crawler-1.jpg', 'RC Off-Road Rock Crawler - main photo', 1, 0, '2026-10-01 13:33:00'),
(181, 'assets/images/products/boys-toys/rc-off-road-rock-crawler-2.jpg', 'RC Off-Road Rock Crawler - second photo', 0, 1, '2026-10-01 13:33:00'),
(182, 'assets/images/products/boys-toys/rc-sports-racing-car-1.jpg', 'RC Sports Racing Car - main photo', 1, 0, '2026-10-01 20:46:00'),
(182, 'assets/images/products/boys-toys/rc-sports-racing-car-2.jpg', 'RC Sports Racing Car - second photo', 0, 1, '2026-10-01 20:46:00'),
(183, 'assets/images/products/boys-toys/monster-truck-1.jpg', 'Monster Truck - main photo', 1, 0, '2026-10-02 03:59:00'),
(183, 'assets/images/products/boys-toys/monster-truck-2.jpg', 'Monster Truck - second photo', 0, 1, '2026-10-02 03:59:00'),
(184, 'assets/images/products/boys-toys/toy-excavator-1.jpg', 'Toy Excavator - main photo', 1, 0, '2026-10-02 10:12:00'),
(184, 'assets/images/products/boys-toys/toy-excavator-2.jpg', 'Toy Excavator - second photo', 0, 1, '2026-10-02 10:12:00'),
(185, 'assets/images/products/boys-toys/construction-wheel-loader-1.jpg', 'Construction Wheel Loader - main photo', 1, 0, '2026-10-02 17:25:00'),
(185, 'assets/images/products/boys-toys/construction-wheel-loader-2.jpg', 'Construction Wheel Loader - second photo', 0, 1, '2026-10-02 17:25:00'),
(186, 'assets/images/products/boys-toys/yellow-dump-truck-1.jpg', 'Yellow Dump Truck - main photo', 1, 0, '2026-10-03 00:38:00'),
(186, 'assets/images/products/boys-toys/yellow-dump-truck-2.jpg', 'Yellow Dump Truck - second photo', 0, 1, '2026-10-03 00:38:00'),
(187, 'assets/images/products/boys-toys/dinosaur-figures-set-12-pcs-1.jpg', 'Dinosaur Figures Set (12 pcs) - main photo', 1, 0, '2026-10-03 07:51:00'),
(187, 'assets/images/products/boys-toys/dinosaur-figures-set-12-pcs-2.jpg', 'Dinosaur Figures Set (12 pcs) - second photo', 0, 1, '2026-10-03 07:51:00'),
(188, 'assets/images/products/boys-toys/t-rex-dinosaur-figure-1.jpg', 'T-Rex Dinosaur Figure - main photo', 1, 0, '2026-10-03 14:04:00'),
(188, 'assets/images/products/boys-toys/t-rex-dinosaur-figure-2.jpg', 'T-Rex Dinosaur Figure - second photo', 0, 1, '2026-10-03 14:04:00'),
(189, 'assets/images/products/boys-toys/classic-tin-robot-1.jpg', 'Classic Tin Robot - main photo', 1, 0, '2026-10-03 21:17:00'),
(189, 'assets/images/products/boys-toys/classic-tin-robot-2.jpg', 'Classic Tin Robot - second photo', 0, 1, '2026-10-03 21:17:00'),
(190, 'assets/images/products/boys-toys/retro-robot-friends-set-1.jpg', 'Retro Robot Friends Set - main photo', 1, 0, '2026-10-04 04:30:00'),
(190, 'assets/images/products/boys-toys/retro-robot-friends-set-2.jpg', 'Retro Robot Friends Set - second photo', 0, 1, '2026-10-04 04:30:00'),
(191, 'assets/images/products/boys-toys/steam-train-railway-set-1.jpg', 'Steam Train Railway Set - main photo', 1, 0, '2026-10-04 11:43:00'),
(191, 'assets/images/products/boys-toys/steam-train-railway-set-2.jpg', 'Steam Train Railway Set - second photo', 0, 1, '2026-10-04 11:43:00'),
(192, 'assets/images/products/boys-toys/rescue-fire-truck-1.jpg', 'Rescue Fire Truck - main photo', 1, 0, '2026-10-04 18:56:00'),
(192, 'assets/images/products/boys-toys/rescue-fire-truck-2.jpg', 'Rescue Fire Truck - second photo', 0, 1, '2026-10-04 18:56:00'),
(193, 'assets/images/products/boys-toys/red-biplane-toy-1.jpg', 'Red Biplane Toy - main photo', 1, 0, '2026-10-05 01:09:00'),
(193, 'assets/images/products/boys-toys/red-biplane-toy-2.jpg', 'Red Biplane Toy - second photo', 0, 1, '2026-10-05 01:09:00'),
(194, 'assets/images/products/boys-toys/building-bricks-set-500-pcs-1.jpg', 'Building Bricks Set (500 pcs) - main photo', 1, 0, '2026-10-05 08:22:00'),
(194, 'assets/images/products/boys-toys/building-bricks-set-500-pcs-2.jpg', 'Building Bricks Set (500 pcs) - second photo', 0, 1, '2026-10-05 08:22:00'),
(195, 'assets/images/products/boys-toys/racing-car-track-set-1.jpg', 'Racing Car Track Set - main photo', 1, 0, '2026-10-05 15:35:00'),
(195, 'assets/images/products/boys-toys/racing-car-track-set-2.jpg', 'Racing Car Track Set - second photo', 0, 1, '2026-10-05 15:35:00'),
(196, 'assets/images/products/boys-toys/mini-camera-drone-1.jpg', 'Mini Camera Drone - main photo', 1, 0, '2026-10-05 22:48:00'),
(196, 'assets/images/products/boys-toys/mini-camera-drone-2.jpg', 'Mini Camera Drone - second photo', 0, 1, '2026-10-05 22:48:00'),
(197, 'assets/images/products/boys-toys/pirate-ship-model-1.jpg', 'Pirate Ship Model - main photo', 1, 0, '2026-10-06 05:01:00'),
(197, 'assets/images/products/boys-toys/pirate-ship-model-2.jpg', 'Pirate Ship Model - second photo', 0, 1, '2026-10-06 05:01:00'),
(198, 'assets/images/products/boys-toys/police-patrol-car-1.jpg', 'Police Patrol Car - main photo', 1, 0, '2026-10-06 12:14:00'),
(198, 'assets/images/products/boys-toys/police-patrol-car-2.jpg', 'Police Patrol Car - second photo', 0, 1, '2026-10-06 12:14:00'),
(199, 'assets/images/products/boys-toys/die-cast-cars-collection-1.jpg', 'Die-Cast Cars Collection - main photo', 1, 0, '2026-10-06 19:27:00'),
(199, 'assets/images/products/boys-toys/die-cast-cars-collection-2.jpg', 'Die-Cast Cars Collection - second photo', 0, 1, '2026-10-06 19:27:00'),
(200, 'assets/images/products/boys-toys/yellow-school-bus-1.jpg', 'Yellow School Bus - main photo', 1, 0, '2026-10-07 02:40:00'),
(200, 'assets/images/products/boys-toys/yellow-school-bus-2.jpg', 'Yellow School Bus - second photo', 0, 1, '2026-10-07 02:40:00');

-- ---------- 3) Product discounts ----------
INSERT INTO `discounts` (`title`, `percentage`, `product_id`, `category_id`, `starts_at`, `ends_at`, `is_active`) VALUES
('Microscope Special', 20.00, 105, NULL, '2026-10-20', '2026-11-30', 1),
('Teepee Tent Offer', 25.00, 153, NULL, '2026-10-20', '2026-11-30', 1),
('RC Racing Deal', 12.00, 182, NULL, '2026-10-20', '2026-11-30', 1),
('Plush Friends Promo', 10.00, 144, NULL, '2026-10-20', '2026-12-15', 1);

-- ---------- 4) Reviews from existing customer accounts (users 1 and 4) ----------
INSERT INTO `reviews` (`product_id`, `user_id`, `rating`, `comment`, `is_visible`, `created_at`) VALUES
(110, 1, 5, 'Beautiful wooden pieces, my son learned the moves in one weekend.', 1, '2026-10-15 10:00:00'),
(110, 4, 4, 'Good quality board, the folding hinge could be a bit stronger.', 1, '2026-10-16 03:00:00'),
(101, 1, 5, 'Bright colours and very solid. Great for practising maths at home.', 1, '2026-10-16 20:00:00'),
(105, 4, 5, 'My daughter checks every leaf in the garden now. Clear image and easy to focus.', 1, '2026-10-17 13:00:00'),
(161, 1, 5, 'Light enough for my 3 year old to carry. He was gliding after two days.', 1, '2026-10-18 06:00:00'),
(144, 4, 5, 'So soft! It is the first thing she takes to bed every night.', 1, '2026-10-18 23:00:00'),
(124, 1, 4, 'Lovely dollhouse, assembly took about an hour but the result is worth it.', 1, '2026-10-19 16:00:00'),
(181, 4, 5, 'Climbs everything in the backyard and the battery lasts long.', 1, '2026-10-20 09:00:00'),
(194, 1, 5, 'Fits with our other bricks perfectly, lots of colours.', 1, '2026-10-21 02:00:00'),
(187, 4, 4, 'Detailed figures, kids love the name cards.', 1, '2026-10-21 19:00:00'),
(125, 4, 5, 'Sturdy and beautiful. My kids cook for us every evening now.', 1, '2026-10-22 12:00:00'),
(108, 1, 4, 'Fun experiments with clear instructions. Needs adult help for some steps.', 1, '2026-10-23 05:00:00'),
(172, 1, 5, 'Comfortable and adjustable, perfect birthday gift.', 1, '2026-10-23 22:00:00'),
(121, 4, 5, 'Magnets connect easily and the wood feels premium.', 1, '2026-10-24 15:00:00'),
(153, 1, 5, 'Turned her corner into a reading nook. Very cute.', 1, '2026-10-25 08:00:00'),
(109, 4, 4, 'Turns smoothly, my son solved it after watching the beginner guide.', 1, '2026-10-26 01:00:00');

-- ===================== PART B: missing team data =====================
-- ---------- 1) Team products: main image + two rows in `images` ----------
UPDATE `products` SET `image` = 'assets/images/products/educational-toys/wooden-alphabet-and-number-puzzle-board-1.jpg' WHERE `id` = 20;
UPDATE `products` SET `image` = 'assets/images/products/educational-toys/counting-beads-abacus-1.jpg' WHERE `id` = 21;
UPDATE `products` SET `image` = 'assets/images/products/educational-toys/shape-sorting-cube-1.jpg' WHERE `id` = 22;
UPDATE `products` SET `image` = 'assets/images/products/educational-toys/flash-card-learning-bundle-1.jpg' WHERE `id` = 23;
UPDATE `products` SET `image` = 'assets/images/products/educational-toys/magnetic-spelling-board-1.jpg' WHERE `id` = 24;
UPDATE `products` SET `image` = 'assets/images/products/educational-toys/solar-system-planetarium-model-1.jpg' WHERE `id` = 25;
UPDATE `products` SET `image` = 'assets/images/products/educational-toys/geo-board-graphic-array-1.jpg' WHERE `id` = 26;
UPDATE `products` SET `image` = 'assets/images/products/educational-toys/kids-microscope-starter-kit-1.jpg' WHERE `id` = 27;
UPDATE `products` SET `image` = 'assets/images/products/educational-toys/clock-telling-time-puzzle-1.jpg' WHERE `id` = 28;
UPDATE `products` SET `image` = 'assets/images/products/educational-toys/montessori-fraction-circles-1.jpg' WHERE `id` = 29;
UPDATE `products` SET `image` = 'assets/images/products/wooden-toys/stacking-wooden-rainbow-rings-1.jpg' WHERE `id` = 30;
UPDATE `products` SET `image` = 'assets/images/products/wooden-toys/wooden-pound-a-peg-bench-1.jpg' WHERE `id` = 31;
UPDATE `products` SET `image` = 'assets/images/products/wooden-toys/wooden-train-and-station-set-1.jpg' WHERE `id` = 32;
UPDATE `products` SET `image` = 'assets/images/products/wooden-toys/wooden-pull-along-duck-1.jpg' WHERE `id` = 33;
UPDATE `products` SET `image` = 'assets/images/products/wooden-toys/natural-wooden-building-blocks-1.jpg' WHERE `id` = 34;
UPDATE `products` SET `image` = 'assets/images/products/wooden-toys/wooden-tool-bench-playset-1.jpg' WHERE `id` = 35;
UPDATE `products` SET `image` = 'assets/images/products/wooden-toys/wooden-kitchen-cutting-fruits-1.jpg' WHERE `id` = 36;
UPDATE `products` SET `image` = 'assets/images/products/wooden-toys/wooden-lacing-beads-1.jpg' WHERE `id` = 37;
UPDATE `products` SET `image` = 'assets/images/products/wooden-toys/wooden-xylophone-musical-toy-1.jpg' WHERE `id` = 38;
UPDATE `products` SET `image` = 'assets/images/products/wooden-toys/wooden-noahs-ark-playset-1.jpg' WHERE `id` = 39;
UPDATE `products` SET `image` = 'assets/images/products/plush-soft-toys/organic-cotton-baby-rattle-set-1.jpg' WHERE `id` = 40;
UPDATE `products` SET `image` = 'assets/images/products/plush-soft-toys/fluffy-teddy-bear-1.jpg' WHERE `id` = 41;
UPDATE `products` SET `image` = 'assets/images/products/plush-soft-toys/musical-lullaby-star-plush-1.jpg' WHERE `id` = 42;
UPDATE `products` SET `image` = 'assets/images/products/plush-soft-toys/soft-crinkle-cloth-book-1.jpg' WHERE `id` = 43;
UPDATE `products` SET `image` = 'assets/images/products/plush-soft-toys/plush-puppy-activity-toy-1.jpg' WHERE `id` = 44;
UPDATE `products` SET `image` = 'assets/images/products/plush-soft-toys/stuffed-bunny-with-long-ears-1.jpg' WHERE `id` = 45;
UPDATE `products` SET `image` = 'assets/images/products/plush-soft-toys/squeaker-elephant-toy-1.jpg' WHERE `id` = 46;
UPDATE `products` SET `image` = 'assets/images/products/plush-soft-toys/comfy-security-blanket-lovey-1.jpg' WHERE `id` = 47;
UPDATE `products` SET `image` = 'assets/images/products/plush-soft-toys/plush-unicorn-pillow-1.jpg' WHERE `id` = 48;
UPDATE `products` SET `image` = 'assets/images/products/plush-soft-toys/weighted-dinosaur-plush-1.jpg' WHERE `id` = 49;
UPDATE `products` SET `image` = 'assets/images/products/active-play-ride-ons/toddler-push-along-walker-1.jpg' WHERE `id` = 50;
UPDATE `products` SET `image` = 'assets/images/products/active-play-ride-ons/3-wheel-lean-to-steer-scooter-1.jpg' WHERE `id` = 51;
UPDATE `products` SET `image` = 'assets/images/products/active-play-ride-ons/inflatable-hopper-bouncy-animal-1.jpg' WHERE `id` = 52;
UPDATE `products` SET `image` = 'assets/images/products/active-play-ride-ons/toddler-balance-bike-1.jpg' WHERE `id` = 53;
UPDATE `products` SET `image` = 'assets/images/products/active-play-ride-ons/soft-foam-play-tunnel-1.jpg' WHERE `id` = 54;

DELETE FROM `images` WHERE `product_id` BETWEEN 20 AND 54;
INSERT INTO `images` (`product_id`, `image_path`, `alt_text`, `is_primary`, `sort_order`) VALUES
(20, 'assets/images/products/educational-toys/wooden-alphabet-and-number-puzzle-board-1.jpg', 'Wooden Alphabet & Number Puzzle Board - main photo', 1, 0),
(20, 'assets/images/products/educational-toys/wooden-alphabet-and-number-puzzle-board-2.jpg', 'Wooden Alphabet & Number Puzzle Board - second photo', 0, 1),
(21, 'assets/images/products/educational-toys/counting-beads-abacus-1.jpg', 'Counting Beads Abacus - main photo', 1, 0),
(21, 'assets/images/products/educational-toys/counting-beads-abacus-2.jpg', 'Counting Beads Abacus - second photo', 0, 1),
(22, 'assets/images/products/educational-toys/shape-sorting-cube-1.jpg', 'Shape Sorting Cube - main photo', 1, 0),
(22, 'assets/images/products/educational-toys/shape-sorting-cube-2.jpg', 'Shape Sorting Cube - second photo', 0, 1),
(23, 'assets/images/products/educational-toys/flash-card-learning-bundle-1.jpg', 'Flash Card Learning Bundle - main photo', 1, 0),
(23, 'assets/images/products/educational-toys/flash-card-learning-bundle-2.jpg', 'Flash Card Learning Bundle - second photo', 0, 1),
(24, 'assets/images/products/educational-toys/magnetic-spelling-board-1.jpg', 'Magnetic Spelling Board - main photo', 1, 0),
(24, 'assets/images/products/educational-toys/magnetic-spelling-board-2.jpg', 'Magnetic Spelling Board - second photo', 0, 1),
(25, 'assets/images/products/educational-toys/solar-system-planetarium-model-1.jpg', 'Solar System Planetarium Model - main photo', 1, 0),
(25, 'assets/images/products/educational-toys/solar-system-planetarium-model-2.jpg', 'Solar System Planetarium Model - second photo', 0, 1),
(26, 'assets/images/products/educational-toys/geo-board-graphic-array-1.jpg', 'Geo-Board Graphic Array - main photo', 1, 0),
(26, 'assets/images/products/educational-toys/geo-board-graphic-array-2.jpg', 'Geo-Board Graphic Array - second photo', 0, 1),
(27, 'assets/images/products/educational-toys/kids-microscope-starter-kit-1.jpg', 'Kids Microscope Starter Kit - main photo', 1, 0),
(27, 'assets/images/products/educational-toys/kids-microscope-starter-kit-2.jpg', 'Kids Microscope Starter Kit - second photo', 0, 1),
(28, 'assets/images/products/educational-toys/clock-telling-time-puzzle-1.jpg', 'Clock Telling Time Puzzle - main photo', 1, 0),
(28, 'assets/images/products/educational-toys/clock-telling-time-puzzle-2.jpg', 'Clock Telling Time Puzzle - second photo', 0, 1),
(29, 'assets/images/products/educational-toys/montessori-fraction-circles-1.jpg', 'Montessori Fraction Circles - main photo', 1, 0),
(29, 'assets/images/products/educational-toys/montessori-fraction-circles-2.jpg', 'Montessori Fraction Circles - second photo', 0, 1),
(30, 'assets/images/products/wooden-toys/stacking-wooden-rainbow-rings-1.jpg', 'Stacking Wooden Rainbow Rings - main photo', 1, 0),
(30, 'assets/images/products/wooden-toys/stacking-wooden-rainbow-rings-2.jpg', 'Stacking Wooden Rainbow Rings - second photo', 0, 1),
(31, 'assets/images/products/wooden-toys/wooden-pound-a-peg-bench-1.jpg', 'Wooden Pound a Peg Bench - main photo', 1, 0),
(31, 'assets/images/products/wooden-toys/wooden-pound-a-peg-bench-2.jpg', 'Wooden Pound a Peg Bench - second photo', 0, 1),
(32, 'assets/images/products/wooden-toys/wooden-train-and-station-set-1.jpg', 'Classic Wooden Train Set - main photo', 1, 0),
(32, 'assets/images/products/wooden-toys/wooden-train-and-station-set-2.jpg', 'Classic Wooden Train Set - second photo', 0, 1),
(33, 'assets/images/products/wooden-toys/wooden-pull-along-duck-1.jpg', 'Wooden Pull Along Duck - main photo', 1, 0),
(33, 'assets/images/products/wooden-toys/wooden-pull-along-duck-2.jpg', 'Wooden Pull Along Duck - second photo', 0, 1),
(34, 'assets/images/products/wooden-toys/natural-wooden-building-blocks-1.jpg', 'Natural Wooden Building Blocks - main photo', 1, 0),
(34, 'assets/images/products/wooden-toys/natural-wooden-building-blocks-2.jpg', 'Natural Wooden Building Blocks - second photo', 0, 1),
(35, 'assets/images/products/wooden-toys/wooden-tool-bench-playset-1.jpg', 'Wooden Tool Bench Playset - main photo', 1, 0),
(35, 'assets/images/products/wooden-toys/wooden-tool-bench-playset-2.jpg', 'Wooden Tool Bench Playset - second photo', 0, 1),
(36, 'assets/images/products/wooden-toys/wooden-kitchen-cutting-fruits-1.jpg', 'Wooden Kitchen Cutting Fruits - main photo', 1, 0),
(36, 'assets/images/products/wooden-toys/wooden-kitchen-cutting-fruits-2.jpg', 'Wooden Kitchen Cutting Fruits - second photo', 0, 1),
(37, 'assets/images/products/wooden-toys/wooden-lacing-beads-1.jpg', 'Wooden Lacing Beads - main photo', 1, 0),
(37, 'assets/images/products/wooden-toys/wooden-lacing-beads-2.jpg', 'Wooden Lacing Beads - second photo', 0, 1),
(38, 'assets/images/products/wooden-toys/wooden-xylophone-musical-toy-1.jpg', 'Wooden Xylophone Musical Toy - main photo', 1, 0),
(38, 'assets/images/products/wooden-toys/wooden-xylophone-musical-toy-2.jpg', 'Wooden Xylophone Musical Toy - second photo', 0, 1),
(39, 'assets/images/products/wooden-toys/wooden-noahs-ark-playset-1.jpg', 'Wooden Noahs Ark Playset - main photo', 1, 0),
(39, 'assets/images/products/wooden-toys/wooden-noahs-ark-playset-2.jpg', 'Wooden Noahs Ark Playset - second photo', 0, 1),
(40, 'assets/images/products/plush-soft-toys/organic-cotton-baby-rattle-set-1.jpg', 'Organic Cotton Baby Rattle Set - main photo', 1, 0),
(40, 'assets/images/products/plush-soft-toys/organic-cotton-baby-rattle-set-2.jpg', 'Organic Cotton Baby Rattle Set - second photo', 0, 1),
(41, 'assets/images/products/plush-soft-toys/fluffy-teddy-bear-1.jpg', 'Fluffy Teddy Bear - main photo', 1, 0),
(41, 'assets/images/products/plush-soft-toys/fluffy-teddy-bear-2.jpg', 'Fluffy Teddy Bear - second photo', 0, 1),
(42, 'assets/images/products/plush-soft-toys/musical-lullaby-star-plush-1.jpg', 'Musical Lullaby Star Plush - main photo', 1, 0),
(42, 'assets/images/products/plush-soft-toys/musical-lullaby-star-plush-2.jpg', 'Musical Lullaby Star Plush - second photo', 0, 1),
(43, 'assets/images/products/plush-soft-toys/soft-crinkle-cloth-book-1.jpg', 'Soft Crinkle Cloth Book - main photo', 1, 0),
(43, 'assets/images/products/plush-soft-toys/soft-crinkle-cloth-book-2.jpg', 'Soft Crinkle Cloth Book - second photo', 0, 1),
(44, 'assets/images/products/plush-soft-toys/plush-puppy-activity-toy-1.jpg', 'Plush Puppy Activity Toy - main photo', 1, 0),
(44, 'assets/images/products/plush-soft-toys/plush-puppy-activity-toy-2.jpg', 'Plush Puppy Activity Toy - second photo', 0, 1),
(45, 'assets/images/products/plush-soft-toys/stuffed-bunny-with-long-ears-1.jpg', 'Stuffed Bunny with Long Ears - main photo', 1, 0),
(45, 'assets/images/products/plush-soft-toys/stuffed-bunny-with-long-ears-2.jpg', 'Stuffed Bunny with Long Ears - second photo', 0, 1),
(46, 'assets/images/products/plush-soft-toys/squeaker-elephant-toy-1.jpg', 'Squeaker Elephant Toy - main photo', 1, 0),
(46, 'assets/images/products/plush-soft-toys/squeaker-elephant-toy-2.jpg', 'Squeaker Elephant Toy - second photo', 0, 1),
(47, 'assets/images/products/plush-soft-toys/comfy-security-blanket-lovey-1.jpg', 'Comfy Security Blanket Lovey - main photo', 1, 0),
(47, 'assets/images/products/plush-soft-toys/comfy-security-blanket-lovey-2.jpg', 'Comfy Security Blanket Lovey - second photo', 0, 1),
(48, 'assets/images/products/plush-soft-toys/plush-unicorn-pillow-1.jpg', 'Plush Unicorn Pillow - main photo', 1, 0),
(48, 'assets/images/products/plush-soft-toys/plush-unicorn-pillow-2.jpg', 'Plush Unicorn Pillow - second photo', 0, 1),
(49, 'assets/images/products/plush-soft-toys/weighted-dinosaur-plush-1.jpg', 'Weighted Dinosaur Plush - main photo', 1, 0),
(49, 'assets/images/products/plush-soft-toys/weighted-dinosaur-plush-2.jpg', 'Weighted Dinosaur Plush - second photo', 0, 1),
(50, 'assets/images/products/active-play-ride-ons/toddler-push-along-walker-1.jpg', 'Toddler Push Along Walker - main photo', 1, 0),
(50, 'assets/images/products/active-play-ride-ons/toddler-push-along-walker-2.jpg', 'Toddler Push Along Walker - second photo', 0, 1),
(51, 'assets/images/products/active-play-ride-ons/3-wheel-lean-to-steer-scooter-1.jpg', '3-Wheel Lean-to-Steer Scooter - main photo', 1, 0),
(51, 'assets/images/products/active-play-ride-ons/3-wheel-lean-to-steer-scooter-2.jpg', '3-Wheel Lean-to-Steer Scooter - second photo', 0, 1),
(52, 'assets/images/products/active-play-ride-ons/inflatable-hopper-bouncy-animal-1.jpg', 'Inflatable Hopper Bouncy Animal - main photo', 1, 0),
(52, 'assets/images/products/active-play-ride-ons/inflatable-hopper-bouncy-animal-2.jpg', 'Inflatable Hopper Bouncy Animal - second photo', 0, 1),
(53, 'assets/images/products/active-play-ride-ons/toddler-balance-bike-1.jpg', 'Toddler Balance Bike - main photo', 1, 0),
(53, 'assets/images/products/active-play-ride-ons/toddler-balance-bike-2.jpg', 'Toddler Balance Bike - second photo', 0, 1),
(54, 'assets/images/products/active-play-ride-ons/soft-foam-play-tunnel-1.jpg', 'Soft Foam Play Tunnel - main photo', 1, 0),
(54, 'assets/images/products/active-play-ride-ons/soft-foam-play-tunnel-2.jpg', 'Soft Foam Play Tunnel - second photo', 0, 1);

-- ---------- 2) Category pictures (the code reads them from uploads/categories) ----------
UPDATE `categories` SET `image` = 'uploads/categories/educational-toys.jpg' WHERE `id` = 20;
UPDATE `categories` SET `image` = 'uploads/categories/wooden-toys.jpg' WHERE `id` = 21;
UPDATE `categories` SET `image` = 'uploads/categories/plush-soft-toys.jpg' WHERE `id` = 22;
UPDATE `categories` SET `image` = 'uploads/categories/active-play-ride-ons.jpg' WHERE `id` = 23;

-- ---------- 3) Age group pictures that pointed to missing files ----------
UPDATE `age_groups` SET `image` = 'assets/images/age-groups/toddlers.jpg' WHERE `slug` = 'toddlers';
UPDATE `age_groups` SET `image` = 'assets/images/age-groups/little-kids.jpg' WHERE `slug` = 'little-kids';

COMMIT;


-- ---------- 4) Column used by the profile page (outside the transaction: ALTER auto-commits) ----------
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `profile_image` varchar(255) DEFAULT NULL AFTER `google_id`;
