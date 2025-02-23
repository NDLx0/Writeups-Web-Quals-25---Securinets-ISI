USE newshub;

CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    content TEXT
);

CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT,
    content TEXT,
    created_at DATETIME,
    FOREIGN KEY (article_id) REFERENCES articles(id)
);

INSERT INTO articles (title, content) VALUES
('Pirate News', 'Latest tales from the seven seas, arr!'),
('Treasure Hunt', 'Seek the gold, beware the kraken!'),
('The Black Sails', 'A ship o’ legend, shrouded in mist and mayhem.'),
('Rum Runner’s Log', 'How Cap’n Flint kept the grog flowin’ through the storm.'),
('The Dread Cove', 'A cursed bay where no sailor dares drop anchor.'),
('Skull Island Secrets', 'Whispers o’ a map etched in bone.'),
('Mutiny at Midnight', 'The crew turned on ol’ Cap’n Blood—here’s how it went down.'),
('The Siren’s Call', 'Beware the song what lures ye to the deep!'),
('Cannonball Chronicles', 'A tally o’ shots fired ‘cross the Spanish Main.'),
('The Ghost Galleon', 'A spectral ship roamin’ the fog, arr!'),
('Gold o’ the Gulch', 'The tale o’ a haul buried ‘neath the cliffs.'),
('Stormrider’s Tale', 'Ridin’ the tempest with nary a sail lost.'),
('The Kraken’s Wake', 'What be left when the beast takes its dive.'),
('Cutlass Clash', 'A duel ‘twixt two rogues fer the last keg o’ rum.'),
('The Jolly Roger Rises', 'How the black flag first flew high.'),
('Barbarossa’s Revenge', 'The pirate king’s wrath on the high seas.'),
('The Mermaid’s Curse', 'A lass o’ the deep with a grudge to settle.'),
('Plunder o’ Port Royal', 'The day the town bled gold and grog.'),
('The Sea Witch’s Brew', 'A potion what turns ye from man to monster.'),
('Dead Man’s Chest', 'Fifteen men on it, and a tale fer each.'),
('The Crimson Tide', 'Blood in the water after a fierce fray.'),
('Voyage o’ the Damned', 'A crew lost to the abyss, arr!');