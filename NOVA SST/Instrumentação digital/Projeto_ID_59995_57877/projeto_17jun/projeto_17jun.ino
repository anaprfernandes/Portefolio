const int pinMedida = A0;        // Definição do pino de entrada para medidas
// Define os pinos dos LEDs
const int led1 = 2;   // propanol
const int led2 = 4;   // atropina
const int led3 = 7;   // pacemaker
const int led4 = 12;  // sem sinal
const int led5 = 13;  // deteção pico R

int taxaAquisicao = 0;           // Taxa de aquisição inicial
bool continuarAquisicao = true;  // Flag para controlar se a aquisição deve continuar
unsigned long tempoA = 0;        // Variável que vai armazenar o tempo da última aquisicao
int intervalo = 0;
unsigned long tempoUltimaOndaR = 0;  // Variável que vai armazenar o tempo da última onda R
unsigned long semSinalTempo = 0;

// Variaveis para cálculo dos BPMs
unsigned long intervaloRR = 0;  // Variável que vai armazenar o intervalo RR
unsigned long BPM = 0;          // Variável que vai armazenar os batimentos por minuto

const int limiarSup = 170;      // Limiar para detecção de onda R
const int limiarInf = 150;      // Limiar para não estar dentro da onda R (barulho)

bool dentroOndaR = false;       // Flag que aponta se a medida é da onda R
int R = 0;                      // Variavel usada para contagem da onda R
unsigned long T1 = 0;           // Tempo da primeira deteção da onda R
unsigned long T2 = 0;           // Tempo da segunda deteção da onda R
int statusled4 = 0;
bool modoAutomatico = false;
int medida;
unsigned long tempoPiscarLED1 = 0;  // Tempo para controlar o piscar do LED 1
bool estadoLED1 = false;            // Estado atual do LED 1 (ligado ou desligado)
unsigned long tempoPiscarLED5 = 0;
bool estadoLED5 = false;



void setup() {
  Serial.begin(9600);     // Inicia a comunicação serial com uma taxa de 9600 bps
  pinMode(led1, OUTPUT);  // Define os pinos dos LEDs como saída
  pinMode(led2, OUTPUT);
  pinMode(led3, OUTPUT);
  pinMode(led4, OUTPUT);
  pinMode(led5, OUTPUT);
}

void loop() {
  unsigned long lastCharReceivedTime = millis();

  if (Serial.available() > 0) {
    char caractereRecebido = Serial.read();

    switch (caractereRecebido) {
      case 'a':
        taxaAquisicao = 25;
        intervalo = 40;
        continuarAquisicao = true;
        tempoUltimaOndaR = millis();  // Reset do tempo de última onda R
        break;

      case 'b':
        taxaAquisicao = 50;
        intervalo = 20;
        continuarAquisicao = true;
        tempoUltimaOndaR = millis();  // Reset do tempo de última onda R
        break;

      case 'c':
        taxaAquisicao = 100;
        intervalo = 10;
        continuarAquisicao = true;
        tempoUltimaOndaR = millis();  // Reset do tempo de última onda R
        break;

      case 'd':
        taxaAquisicao = 200;
        intervalo = 5;
        continuarAquisicao = true;
        tempoUltimaOndaR = millis();  // Reset o tempo de última onda R
        break;

      case 'e':
        continuarAquisicao = false;
        modoAutomatico=false;
        intervalo = 0;
        digitalWrite(led1, LOW);
        digitalWrite(led2, LOW);
        digitalWrite(led3, LOW);
        digitalWrite(led4, LOW);
        break;

      case 'f':
        if (!modoAutomatico) {
          digitalWrite(led1, HIGH);  // Acende LED 1
        }
        break;

      case 'g':
        if (!modoAutomatico) {
          digitalWrite(led1, LOW);  // Apaga LED 1
        }
        break;

      case 'h':
        if (!modoAutomatico) {
          digitalWrite(led2, HIGH);  // Acende LED 2
        }
        break;

      case 'i':
        if (!modoAutomatico) {
          digitalWrite(led2, LOW);  // Apaga LED 2
        }
        break;

      case 'j':
        if (!modoAutomatico) {
          digitalWrite(led3, HIGH);  // Acende LED 3
        }
        break;

      case 'k':
        if (!modoAutomatico) {
          digitalWrite(led3, LOW);  // Apaga LED 3
        }
        break;

      case 'l':
        if (!modoAutomatico) {
          digitalWrite(led4, HIGH);  // Acende LED 4
        }
        break;

      case 'm':
        if (!modoAutomatico) {
          digitalWrite(led4, LOW);  // Apaga LED 4
        }
        break;

      case 'n':
        modoAutomatico = true;
        continuarAquisicao = false;
        intervalo = 0;
        break;

      case 'o':
        modoAutomatico = false;
        continuarAquisicao = false;
        intervalo = 0;
        //desligamos os leds que estavam ligados do controlo automático
        digitalWrite(led1, LOW);
        digitalWrite(led2, LOW);
        digitalWrite(led3, LOW);
        digitalWrite(led4, LOW);
        break;
    }
  }

  if (intervalo > 0) {
    if ((millis() - tempoA > intervalo)) {
      medida = analogRead(pinMedida);
      medida = map(medida, 0, 1023, 0, 255);
      tempoA = millis();
      Serial.write(medida);
    }

    if (modoAutomatico) {
      if (medida > limiarSup && R == 0) {
        R = 1;
        T1 = millis();
        tempoUltimaOndaR = millis();
        dentroOndaR = true;
        digitalWrite(led5, HIGH);
      } else if (medida > limiarSup && R == 1 && dentroOndaR) {
        // Ainda está dentro da onda R mas nada deverá acontecer
      } else if (medida < limiarInf && R == 1) {
        dentroOndaR = false;
        digitalWrite(led5, LOW);
      } else if (medida > limiarSup && R == 1 && !dentroOndaR) {
        T2 = millis();
        dentroOndaR = true;
        intervaloRR = T2 - T1;
        BPM = 60000 / intervaloRR;
        //Serial.println(BPM); // Envia os BPMs como texto
        R = 0;  // Recomeçar contagem de onda

        if (BPM > 160) {
          // Piscar LED1 (propanol) a cada 500ms
          if (millis() - tempoPiscarLED1 >= 500) {
            estadoLED1 = !estadoLED1;                     // Alterna o estado do LED1
            digitalWrite(led1, estadoLED1 ? HIGH : LOW);  // Liga ou desliga o LED1
            tempoPiscarLED1 = millis();                   // Atualiza o tempo de referência para piscar
          }
        } else {
          digitalWrite(led1, LOW);  // Garante que o LED1 está desligado se BPM não for maior que 160
        }

        if (BPM >= 30 && BPM <= 40) {
          digitalWrite(led2, HIGH);
        } else {
          digitalWrite(led2, LOW);  // Garante que o LED2 está desligado se BPM não estiver no intervalo
        }

        if (BPM >= 10 && BPM <= 30) {
          digitalWrite(led3, HIGH);
        } else {
          digitalWrite(led3, LOW);  // Garante que o LED3 está desligado se BPM não estiver no intervalo
        }
      }
    }
  }

  // Verifica se não foi detectada nenhuma onda R durante 10 segundos
  if (modoAutomatico){
    if ((millis-tempoUltimaOndaR > 10000) && statusled4 == 0) {
      digitalWrite(led4, HIGH);
      statusled4 = 1;
      semSinalTempo = millis();
      BPM = 0; // obrigar a que os BPM sejam zero quando se corta o sinal 
    }
    // Verifica se passaram 5 segundos desde que o LED4 foi ativado
    if ((millis() - semSinalTempo >= 5000) && statusled4==1) {
      digitalWrite(led4, LOW);
      statusled4 = 0;
      tempoUltimaOndaR= millis();
    }
  }
}
