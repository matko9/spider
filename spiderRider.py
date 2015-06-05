import nltk
from nltk.tokenize import RegexpTokenizer

f=open('test.txt','rU');

raw=f.read();

tokenizer = RegexpTokenizer(r'[A-Za-z]\w+');
tokens = tokenizer.tokenize(raw);

fdist1 = nltk.FreqDist(tokens);

bla = fdist1.most_common(100);

print bla;


