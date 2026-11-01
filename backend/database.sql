-- Database schema for Blog Platform (Milestone 2)
-- MySQL 8.0+, utf8mb4

CREATE DATABASE IF NOT EXISTS blog_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE blog_platform;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','user') NOT NULL DEFAULT 'user',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL UNIQUE,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  author_id INT NOT NULL,
  title VARCHAR(200) NOT NULL,
  content MEDIUMTEXT NOT NULL,
  category_id INT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_posts_author FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_posts_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tags (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL UNIQUE,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS comments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  post_id INT NOT NULL,
  user_id INT NULL,
  content TEXT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  status ENUM('visible','hidden') NOT NULL DEFAULT 'visible',
  CONSTRAINT fk_comments_post FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_comments_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS post_tags (
  post_id INT NOT NULL,
  tag_id INT NOT NULL,
  PRIMARY KEY (post_id, tag_id),
  CONSTRAINT fk_pt_post FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_pt_tag FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE INDEX idx_posts_category ON posts(category_id);
CREATE INDEX idx_posts_author ON posts(author_id);
CREATE INDEX idx_comments_post ON comments(post_id);
CREATE INDEX idx_comments_user ON comments(user_id);
CREATE INDEX idx_pt_tag ON post_tags(tag_id);

-- Sample data for a fresh database; explicit IDs keep relationships predictable.
-- Demo passwords: admin = BlogAdmin2026!; regular users = BlogDemo2026!
-- Hashes generated with PHP password_hash(..., PASSWORD_BCRYPT).
START TRANSACTION;

INSERT INTO users (id, name, email, password_hash, role, created_at) VALUES
(1, 'Amina Hadzic', 'amina@example.com', '$2y$12$stTqKHcfG9IdymWPsud3FOBAlsdc7kNU4sccBkAFhciiBUHhzV/Oe', 'admin', '2026-10-01 09:00:00'),
(2, 'Marko Petrovic', 'marko@example.com', '$2y$12$GTdWukns1zTyCLCfBcTabe1bnP/1Q4mwQ1aZpEG84IXLHefVlsGpe', 'user', '2026-10-02 10:30:00'),
(3, 'Lejla Music', 'lejla@example.com', '$2y$12$GTdWukns1zTyCLCfBcTabe1bnP/1Q4mwQ1aZpEG84IXLHefVlsGpe', 'user', '2026-10-03 14:15:00'),
(4, 'Ivan Kovac', 'ivan@example.com', '$2y$12$GTdWukns1zTyCLCfBcTabe1bnP/1Q4mwQ1aZpEG84IXLHefVlsGpe', 'user', '2026-10-04 11:00:00');

INSERT INTO categories (id, name, created_at) VALUES
(1, 'Technology', '2026-10-01 09:15:00'),
(2, 'Travel', '2026-10-01 09:20:00'),
(3, 'Food', '2026-10-01 09:25:00');

INSERT INTO tags (id, name, created_at) VALUES
(1, 'PHP', '2026-10-01 09:30:00'),
(2, 'MySQL', '2026-10-01 09:31:00'),
(3, 'Web Development', '2026-10-01 09:32:00'),
(4, 'Bosnia and Herzegovina', '2026-10-01 09:33:00'),
(5, 'Weekend Trips', '2026-10-01 09:34:00'),
(6, 'Home Cooking', '2026-10-01 09:35:00');

INSERT INTO posts (id, author_id, title, content, category_id, created_at) VALUES
(1, 1, 'Building a small blog with PHP and MySQL', 'Start with a clear schema: users write posts, categories group them, and tags connect related topics. A shared PDO connection and prepared statements keep database access consistent. Test each DAO independently before adding routes and connecting the interface.', 1, '2026-10-10 10:00:00'),
(2, 2, 'What I learned from my first database project', 'Foreign keys caught mistakes that were easy to miss in application code. I also learned to insert parent records before their children and to test deletion rules deliberately. Keeping a small SQL dataset made it much easier to reproduce problems on another computer.', 1, '2026-10-12 15:30:00'),
(3, 3, 'A relaxed weekend in Sarajevo', 'Begin with a morning walk through Bascarsija, then leave time for coffee and a visit to the riverside. Comfortable shoes help on the steeper streets. My favourite part was exploring without a packed itinerary and stopping at small local bakeries along the way.', 2, '2026-10-15 09:00:00'),
(4, 4, 'Planning a day trip to Mostar', 'For a short visit, choose a few places within walking distance and allow time to rest. We spent the morning around the old town and found a quiet lunch spot away from the busiest streets. Check transport times before leaving so the return journey is not rushed.', 2, '2026-10-18 12:00:00'),
(5, 3, 'Homemade vegetable soup for busy evenings', 'Soften one chopped onion in a little oil, then add diced carrots, potatoes and seasonal vegetables. Cover with stock and simmer until tender. Finish with parsley and adjust the seasoning. Leftovers make an easy lunch the next day when cooled and refrigerated promptly.', 3, '2026-10-20 17:00:00');

INSERT INTO comments (id, post_id, user_id, content, created_at, status) VALUES
(1, 1, 2, 'The shared DAO approach helped me remove repeated connection code from my project.', '2026-10-10 13:20:00', 'visible'),
(2, 1, 3, 'A follow-up showing how you test update and delete operations would be useful.', '2026-10-11 08:45:00', 'visible'),
(3, 2, 1, 'Keeping the sample dataset in version control also makes reviews easier.', '2026-10-13 10:00:00', 'visible'),
(4, 3, 4, 'We followed a similar route last weekend. Leaving time for coffee was a good decision.', '2026-10-16 16:10:00', 'visible'),
(5, 4, 2, 'Checking the return schedule beforehand saved us a lot of stress on our last trip.', '2026-10-19 11:30:00', 'visible'),
(6, 5, 4, 'I added white beans to make this a more filling lunch. It worked well.', '2026-10-21 12:15:00', 'visible'),
(7, 3, 2, 'Off-topic promotional comment awaiting moderator review.', '2026-10-22 18:00:00', 'hidden');

INSERT INTO post_tags (post_id, tag_id) VALUES
(1, 1), (1, 2), (1, 3),
(2, 2), (2, 3),
(3, 4), (3, 5),
(4, 4), (4, 5),
(5, 6);

COMMIT;
