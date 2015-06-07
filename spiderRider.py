import nltk, re
from nltk.tokenize import RegexpTokenizer

#open file and read, put all word to lowercase so we have no duplicates
f=open('songLyrics.txt','rU')
raw=f.read()
raw = raw.lower()

#add unnecesarry words to remove list and join them for regex
removeList = ["the", "and", "it", "in", "is", "don", "re", "ll", "ve", "chorus", "na", ]
remove = '|'.join(removeList)

#compile regex and run it on raw text
filterRegex = re.compile(r'\b('+remove+r')\b', flags=re.IGNORECASE);
raw = filterRegex.sub("", raw);

#use tokenizer to match and clean words
tokenizer = RegexpTokenizer(r'[A-Za-z]\w+');
tokens = tokenizer.tokenize(raw);

#use frequency distance on tokens and get 100 most used
fdist1 = nltk.FreqDist(tokens);
words = fdist1.most_common(100);

print words;


