# TI-Project

This is a college project within the scope of the Internet Technologies subject of the Computer Engineering course. 
The project consists of an IoT system with a smart greenhouse theme.

## 📚 Documentation

**[🇵🇹 ESTRUTURA_PROJETO.md](ESTRUTURA_PROJETO.md)** - Documentação completa em Português sobre:
- Estrutura do projeto e arquitetura do sistema
- Como adicionar novos sensores e atuadores
- API e endpoints disponíveis
- Integração com Arduino e Raspberry Pi
- Exemplos práticos e guias passo a passo

Here is the project report:
[Relatorio_Projeto.pdf](https://github.com/user-attachments/files/16892826/Relatorio_Projeto.pdf)

## 🚀 Quick Start

1. **Login**: Access `index.php` with your credentials
2. **Dashboard**: View real-time sensor data and control actuators
3. **Graphs**: Check historical data in `grafico.php`
4. **Images**: View captured webcam images in `historicoimages.php`

## 🏗️ Project Structure

```
TI-Project/
├── index.php              # Login page
├── dashboard.php          # Main dashboard
├── grafico.php           # Data graphs
├── historico.php         # Data history
├── api/                  # REST API backend
│   ├── api.php          # Main endpoint
│   └── files/           # Data storage
├── RPI_ARDUINO/         # IoT device code
│   ├── RPI.py          # Raspberry Pi script
│   └── arduino_get_and_post.ino
└── ESTRUTURA_PROJETO.md # Detailed documentation (PT)
```

## 🌡️ Sensors & Actuators

- **Sensors**: Temperature (DHT11), Humidity, Button
- **Actuators**: LEDs, Relay controls
- **Camera**: Webcam image capture

For detailed information on how to add new sensors/actuators, see [ESTRUTURA_PROJETO.md](ESTRUTURA_PROJETO.md)


