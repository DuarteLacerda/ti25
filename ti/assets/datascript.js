document.addEventListener("DOMContentLoaded", () => {
  // Função para obter a parte inteira de um número
  function parteInteira(valor) {
    return Math.trunc(parseFloat(valor));
  }

  // Função para obter a parte decimal de um número e formatá-lo com 1 casa decimal
  function parteDecimal(valor) {
    const valorNum = parseFloat(valor);
    const parteInteira = Math.trunc(valorNum);
    const parteDecimal = valorNum - parteInteira;
    const valorFinal = Math.round(parteDecimal * 10) / 10 + parteInteira;
    return valorFinal.toFixed(1); // Retorna o valor com 1 casa decimal
  }

  // Função para atualizar os valores dos sensores
  async function atualizarSensores() {
    const scrollPos = window.scrollY || window.pageYOffset;
    try {
      const resposta = await fetch("api/data.php"); // Obtém os dados da API
      const dados = await resposta.json();

      for (const sensor in dados) {
        // Seleciona os elementos HTML correspondentes ao sensor
        const nomeSpan = document.getElementById(`nome-${sensor}`);
        const valorSpan = document.getElementById(`valor-${sensor}`);
        const horaSpan = document.getElementById(`hora-${sensor}`);
        const statuSpan = document.getElementById(`status-${sensor}`);

        if (valorSpan && horaSpan && statuSpan && nomeSpan) {
          let valorBruto = dados[sensor].valor.trim(); // Valor original do sensor
          let valorNum = parseFloat(valorBruto); // Valor numérico para comparação
          let valorFormatado = valorBruto; // Valor formatado para exibição

          // Seleciona os elementos na tabela de sensores
          const nomeSpanTable = document.querySelector(
            `#tabela-sensores td span#nome-${sensor}`
          );

          const valorSpanTable = document.querySelector(
            `#tabela-sensores td span#valor-${sensor}`
          );
          const horaSpanTable = document.querySelector(
            `#tabela-sensores td span#hora-${sensor}`
          );

          // Verifica o tipo de sensor e aplica lógica específica
          switch (sensor) {
            case "led":
              if (valorBruto == "3.0") {
                estado = "Vermelho";
              } else if (valorBruto == "2.0") {
                estado = "Amarelo";
              } else if (valorBruto == "1.0") {
                estado = "Verde";
              } else {
                estado = "Desligado";
              }
              valorFormatado = estado;
              switch (estado) {
                case "Vermelho":
                  statuSpan.innerHTML =
                    "<span class='badge bg-danger'>Ligado</span>";
                  break;
                case "Amarelo":
                  statuSpan.innerHTML =
                    "<span class='badge bg-warning text-dark'>Ligado</span>";
                  break;
                case "Verde":
                  statuSpan.innerHTML =
                    "<span class='badge bg-success'>Ligado</span>";
                  break;
                case "Desligado":
                  statuSpan.innerHTML =
                    "<span class='badge bg-secondary'>Desligado</span>";
                  break;
              }
              break;
            case "temperatura":
              valorFormatado = `${parteDecimal(valorBruto)}ºC`;
              // Define o estado com base no valor da temperatura
              if (valorNum > 40) {
                statuSpan.innerHTML =
                  "<span class='badge bg-danger'>Muito Alta</span>";
              } else if (valorNum > 30 && valorNum < 40) {
                statuSpan.innerHTML =
                  "<span class='badge bg-warning text-dark'>Alta</span>";
              } else if (valorNum > 20 && valorNum < 30) {
                statuSpan.innerHTML =
                  "<span class='badge bg-success'>Normal</span>";
              } else if (valorNum > 10 && valorNum < 20) {
                statuSpan.innerHTML =
                  "<span class='badge bg-primary'>Baixa</span>";
              } else {
                statuSpan.innerHTML =
                  "<span class='badge bg-danger'>Muito Baixa</span>";
              }
              break;
            case "humidade":
              valorFormatado = `${parteDecimal(valorBruto)}%`;
              // Define o estado com base no valor da humidade
              if (valorNum > 89) {
                statuSpan.innerHTML =
                  "<span class='badge bg-danger'>Alta</span>";
              } else if (valorNum < 90 && valorNum > 39) {
                statuSpan.innerHTML =
                  "<span class='badge bg-warning text-dark'>Normal</span>";
              } else if (valorNum < 40 && valorNum > 0) {
                statuSpan.innerHTML =
                  "<span class='badge bg-primary'>Baixa</span>";
              } else if (valorNum < 0) {
                statuSpan.innerHTML =
                  "<span class='badge bg-danger'>Erro no sensor</span>";
              }
              break;
            case "cancela":
              valorFormatado = (valorNum === 1) ? "Aberta" : "Fechada";
              // Define o estado da cancela com base no valor
              if (valorNum === 1) {
              statuSpan.innerHTML =
                "<span class='badge bg-success'>Aberta</span>";
              } else if (valorNum === -1) {
              statuSpan.innerHTML =
                "<span class='badge bg-secondary'>Fechada</span>";
              }
              break;
            case "distancia":
              valorFormatado = `${parteDecimal(valorBruto)} cm`;
              // Define o estado com base na distância
              if (valorNum < 11 && valorNum > 0) {
                statuSpan.innerHTML =
                  "<span class='badge bg-success'>Distancia Curta</span>";
              } else if (valorNum > 10 && valorNum < 20) {
                statuSpan.innerHTML =
                  "<span class='badge bg-warning text-dark'>Distancia Média</span>";
              } else if (valorNum > 20 && valorNum < 30) {
                statuSpan.innerHTML =
                  "<span class='badge bg-warning text-dark'>Distancia Longa</span>";
              } else if (valorNum > 30) {
                statuSpan.innerHTML =
                  "<span class='badge bg-primary'>Muito Longe</span>";
              } else if (valorNum < 0) {
                statuSpan.innerHTML =
                  "<span class='badge bg-danger'>Erro no sensor</span>";
              }
              break;
            case "ventoinha":
              if (valorBruto == "1") {
                estado = "Ligado";
              }
              if (valorBruto == "0") {
                estado = "Desligado";
              }
              valorFormatado = estado;
              switch (estado) {
                case "Ligado":
                  statuSpan.innerHTML =
                    "<span class='badge bg-primary'>Ligado</span>";
                  break;
                case "Desligado":
                  statuSpan.innerHTML =
                    "<span class='badge bg-secondary'>Desligado</span>";
                  break;
              }
              break;
          }

          // Atualiza os valores e horários no HTML
          nomeSpan.textContent =
            dados[sensor].nome.charAt(0).toUpperCase() +
            dados[sensor].nome.slice(1);
          valorSpan.textContent = ` ${valorFormatado}`;
          horaSpan.textContent = dados[sensor].hora;
          nomeSpanTable.textContent =
            dados[sensor].nome.charAt(0).toUpperCase() +
            dados[sensor].nome.slice(1);
          valorSpanTable.textContent = ` ${valorBruto}`;
          horaSpanTable.textContent = dados[sensor].hora;
        }
      }
    } catch (erro) {
      console.error("Erro ao carregar os dados dos sensores:", erro);
    }
    // Restaura a posição de rolagem
    window.scrollTo(0, scrollPos);
  }

  const urlParams = new URLSearchParams(window.location.search);
  const nomeSensor = urlParams.get("nome"); // Obtém o nome do sensor da URL
  let ultimoTimestampHistorico = null;

  // Função para carregar o histórico de um sensor
  async function carregarHistorico() {
    const scrollPos = window.scrollY || window.pageYOffset;

    try {
      if (nomeSensor === "webcam") {
        const resposta = await fetch("api/get_image_history.php");
        const dados = await resposta.json();

        if (!Array.isArray(dados) || dados.length === 0) return;

        // Ultimo timestamp novo
        const ultimoTimestampNovo = dados[0]?.datetime || null;
        if (ultimoTimestampNovo === ultimoTimestampHistorico) {
          console.log("Nenhuma atualização no histórico");
          window.scrollTo(0, scrollPos);
          return;
        }
        ultimoTimestampHistorico = ultimoTimestampNovo;

        // Atualiza a tabela
        const tbody = document.querySelector(`#historico-${nomeSensor} tbody`);
        if (!tbody) {
          console.warn("Elemento tbody para histórico não encontrado.");
          return;
        }
        tbody.innerHTML = "";

        // Preenche as linhas com imagens
        dados.forEach((item) => {
          const tr = document.createElement("tr");

          const tdImage = document.createElement("td");
          const img = document.createElement("img");
          img.src = "api/" + item.image;
          img.alt = "Imagem da Webcam";
          img.style.maxWidth = "150px";
          img.style.maxHeight = "130px";
          tdImage.appendChild(img);

          const tdDate = document.createElement("td");
          tdDate.textContent = item.datetime;

          tr.appendChild(tdImage);
          tr.appendChild(tdDate);

          tbody.appendChild(tr);
        });

        // Não faz gráfico para imagens
      } else {
        // Código original para outros sensores numéricos
        const resposta = await fetch(`api/logs.php?sensor=${nomeSensor}`);
        const dados = await resposta.json();

        const logTexto = dados[nomeSensor]?.log;
        if (!logTexto) return;

        const linhas = logTexto
          .trim()
          .split("\n")
          .filter((l) => l.trim() !== "")
          .slice(-10);

        const ultimoTimestampNovo =
          linhas.length > 0 ? linhas[linhas.length - 1].split(";")[0] : null;

        if (ultimoTimestampNovo === ultimoTimestampHistorico) {
          console.log("Nenhuma atualização no histórico");
          window.scrollTo(0, scrollPos);
          return;
        }

        ultimoTimestampHistorico = ultimoTimestampNovo;

        const tbody = document.querySelector(`#historico-${nomeSensor} tbody`);
        if (!tbody) {
          console.warn("Elemento tbody para histórico não encontrado.");
          return;
        }
        tbody.innerHTML = "";

        const labels = [];
        const data = [];

        linhas.reverse().forEach((linha) => {
          const [dataHora, valor] = linha.split(";");

          const tr = document.createElement("tr");

          labels.push(dataHora.split(" ")[1]);
          data.push(parseFloat(valor));

          let alerta = "";
          const valorNum = parseFloat(valor);

          // ... (aqui mantém o teu switch/case original para alertas)

          // Copiar e colar o teu switch/case aqui, para manteres alertas iguais:
          switch (nomeSensor) {
            case "temperatura":
              if (valorNum > 40)
                alerta = "<span class='badge bg-danger'>Muito Alta</span>";
              else if (valorNum > 30)
                alerta = "<span class='badge bg-warning text-dark'>Alta</span>";
              else if (valorNum > 20)
                alerta = "<span class='badge bg-success'>Normal</span>";
              else if (valorNum > 10)
                alerta = "<span class='badge bg-primary'>Baixa</span>";
              else alerta = "<span class='badge bg-danger'>Muito Baixa</span>";
              break;
            case "humidade":
              if (valorNum > 89)
                alerta = "<span class='badge bg-danger'>Alta</span>";
              else if (valorNum > 39)
                alerta =
                  "<span class='badge bg-warning text-dark'>Normal</span>";
              else if (valorNum >= 0)
                alerta = "<span class='badge bg-primary'>Baixa</span>";
              else
                alerta = "<span class='badge bg-danger'>Erro no sensor</span>";
              break;
            case "distancia":
              if (valorNum > 0 && valorNum < 11)
                alerta =
                  "<span class='badge bg-success'>Distancia Curta</span>";
              else if (valorNum < 20)
                alerta =
                  "<span class='badge bg-warning text-dark'>Distancia Média</span>";
              else if (valorNum < 30)
                alerta =
                  "<span class='badge bg-warning text-dark'>Distancia Longa</span>";
              else if (valorNum >= 30)
                alerta = "<span class='badge bg-primary'>Muito Longa</span>";
              else
                alerta = "<span class='badge bg-danger'>Erro no sensor</span>";
              break;
            case "cancela":
              if (valorNum === 1 || valorNum === 1.0) {
              alerta = "<span class='badge bg-danger'>Aberta</span>";
              } else if (valorNum === -1 || valorNum === -1.0) {
              alerta = "<span class='badge bg-success'>Fechada</span>";
              } else {
              alerta = "<span class='badge bg-secondary'>Desconhecido</span>";
              }
              break;
            case "ventoinha":
              if (valorNum == 1)
                alerta = "<span class='badge bg-primary'>Ligado</span>";
              else if (valorNum == 0)
                alerta = "<span class='badge bg-secondary'>Desligado</span>";
              break;
            case "led":
              switch (valorNum) {
                case 3:
                  alerta = "<span class='badge bg-danger'>Ligado</span>";
                  break;
                case 1:
                  alerta = "<span class='badge bg-success'>Ligado</span>";
                  break;
                case 2:
                  alerta =
                    "<span class='badge bg-warning text-dark'>Ligado</span>";
                  break;
                default:
                  alerta = "<span class='badge bg-primary'>Desligado</span>";
                  break;
              }
              break;
            default:
              alerta = "<span class='badge bg-secondary'>Desconhecido</span>";
              break;
          }

          tr.innerHTML = `
            <td>${valor}</td>
            <td>${dataHora}</td>
            <td>${alerta}</td>
          `;

          tbody.appendChild(tr);
        });

        if (window.chartHistorico) {
          window.chartHistorico.destroy();
        }

        labels.reverse();
        data.reverse();

        window.chartHistorico = new Chart(
          document.getElementById("chartjs-line"),
          {
            type: "line",
            data: {
              labels: labels,
              datasets: [
                {
                  label: nomeSensor,
                  borderWidth: 1,
                  pointRadius: 5,
                  pointHoverRadius: 7,
                  pointBackgroundColor: "blue",
                  backgroundColor: "transparent",
                  borderColor: "black",
                  data: data,
                  tension: 0.2,
                },
              ],
            },
            options: {
              scales: {
                x: {
                  grid: { color: "rgba(0,0,0,0.0)" },
                  ticks: { autoSkip: true, maxTicksLimit: 10 },
                },
                y: {
                  grid: { color: "rgba(0,0,0,0.0)" },
                },
              },
            },
          }
        );
      }
    } catch (erro) {
      console.error("Erro ao carregar o histórico:", erro);
    }

    window.scrollTo(0, scrollPos);
  }

  // Atualiza a imagem da webcam e a hora da última atualização
  async function atualizarWebcam() {
    try {
      const resposta = await fetch("api/get_latest_image.php");
      const dados = await resposta.json();

      if (dados.path) {
        const imagem = document.getElementById("imagem-webcam");
        const hora = document.getElementById("hora-webcam");

        if (!imagem || !hora) {
          return;
        }

        imagem.src = "api/" + dados.path + "?t=" + new Date().getTime();
        hora.textContent = dados.hora;
      }
    } catch (erro) {
      console.error("Erro ao atualizar a webcam:", erro);
    }
  }

  // Atualiza a webcam a cada 3 segundos
  setInterval(atualizarWebcam, 3000);
  atualizarWebcam(); // Chamada inicial

  // Atualiza os sensores a cada 3 segundos
  setInterval(atualizarSensores, 3000);
  atualizarSensores(); // Chamada inicial

  // Atualiza o histórico a cada 3 segundos se o nome do sensor estiver na URL
  if (nomeSensor) {
    setInterval(carregarHistorico, 3000);
  }
  carregarHistorico(); // Chamada inicial
});
