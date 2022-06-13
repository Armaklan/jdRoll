class Forum {
  constructor(campagne_id) {
    this.campagne_id = campagne_id;
  }
}

class Section {
  constructor(data) {
    this.id = data.id;
    this.label = data.label;
    this.orderIndex = data.orderIndex;
    this.isFold = data.isFold;
    this.topics = [];
  }

  addTopic(topic) {
    this.topics.push(topic);
  }
}

class Topic {
  constructor(data) {
    this.id = data.id;
    this.label = data.label;
    this.orderIndex = data.orderIndex;
    this.posts = [];
  }

  addPosts(post) {
      this.posts.push(post);
  }
}

class Post {
  constructor(data) {
    this.id = data.id;
    this.htmlContent = data.content;
    this.orderIndex = data.orderIndex;
  }
  setCharacter(character) {
    this.character = character;
  }
  setUser(user) {
    this.user = user;
  }
  setPlace(place) {
    this.place = place;
  }
}
