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
              if (valorBruto == "3") {
                estado = "Vermelho";
              }
              if (valorBruto == "2") {
                estado = "Amarelo";
              }
              if (valorBruto == "1") {
                estado = "Verde";
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
              valorFormatado = valorBruto == "50" ? "Aberta" : "Fechada";
              // Define o estado da cancela com base no valor
              if (valorNum >= 140 && valorNum <= 180) {
                statuSpan.innerHTML =
                  "<span class='badge bg-danger'>Fechada</span>";
              } else if (valorNum >= 30 && valorNum <= 70) {
                statuSpan.innerHTML =
                  "<span class='badge bg-success'>Aberta</span>";
              } else if (valorNum < 0) {
                statuSpan.innerHTML =
                  "<span class='badge bg-danger'>Erro no sensor</span>";
              } else {
                statuSpan.innerHTML =
                  "<span class='badge bg-warning text-dark'>Estado indefinido</span>";
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
              valorFormatado = `${parteInteira(valorBruto)} RPM`;
              // Define o estado com base na velocidade da ventoinha
              if (valorNum > 2000) {
                statuSpan.innerHTML =
                  "<span class='badge bg-danger'>Alto</span>";
              } else if (valorNum < 1000) {
                statuSpan.innerHTML =
                  "<span class='badge bg-primary'>Baixo</span>";
              } else {
                statuSpan.innerHTML =
                  "<span class='badge bg-success'>Normal</span>";
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
  }

  const urlParams = new URLSearchParams(window.location.search);
  const nomeSensor = urlParams.get("nome"); // Obtém o nome do sensor da URL

  // Função para carregar o histórico de um sensor
  async function carregarHistorico() {
    try {
      const resposta = await fetch(`api/logs.php?sensor=${nomeSensor}`);
      const dados = await resposta.json();

      const logTexto = dados[nomeSensor]?.log;
      if (!logTexto) return;

      const linhas = logTexto
        .trim()
        .split("\n")
        .filter((l) => l.trim() !== "");
      const tbody = document.querySelector(`#historico-${nomeSensor} tbody`);
      tbody.innerHTML = "";

      linhas.reverse().forEach((linha) => {
        const [dataHora, valor] = linha.split(";");
        const tr = document.createElement("tr");

        // Define alertas com base no tipo de sensor
        let alerta = "";
        const valorNum = parseFloat(valor);

        switch (nomeSensor) {
          case "temperatura":
            if (valorNum > 40) {
              alerta = "<span class='badge bg-danger'>Muito Alta</span>";
            } else if (valorNum < 40 && valorNum > 30) {
              alerta = "<span class='badge bg-warning text-dark'>Alta</span>";
            } else if (valorNum < 30 && valorNum > 20) {
              alerta = "<span class='badge bg-success'>Normal</span>";
            } else if (valorNum < 20 && valorNum > 10) {
              alerta = "<span class='badge bg-primary'>Baixa</span>";
            } else if (valorNum < 10) {
              alerta = "<span class='badge bg-danger'>Muito Baixa</span>";
            }
            break;
          case "humidade":
            if (valorNum > 89) {
              alerta = "<span class='badge bg-danger'>Alta</span>";
            } else if (valorNum < 90 && valorNum > 39) {
              alerta = "<span class='badge bg-warning text-dark'>Normal</span>";
            } else if (valorNum < 40 && valorNum > 0) {
              alerta = "<span class='badge bg-primary'>Baixa</span>";
            } else if (valorNum < 0) {
              alerta = "<span class='badge bg-danger'>Erro no sensor</span>";
            }
            break;
          case "distancia":
            if (valorNum < 11 && valorNum > 0) {
              alerta = "<span class='badge bg-success'>Distancia Curta</span>";
            } else if (valorNum > 10 && valorNum < 20) {
              alerta =
                "<span class='badge bg-warning text-dark'>Distancia Média</span>";
            } else if (valorNum > 20 && valorNum < 30) {
              alerta =
                "<span class='badge bg-warning text-dark'>Distancia Longa</span>";
            } else if (valorNum > 30) {
              alerta = "<span class='badge bg-primary'>Muito Longa</span>";
            } else if (valorNum < 0) {
              alerta = "<span class='badge bg-danger'>Erro no sensor</span>";
            }
            break;
          case "cancela":
            if (valorNum > 50) {
              alerta = "<span class='badge bg-success'>Fechada</span>";
            } else if (valorNum < 51) {
              alerta = "<span class='badge bg-danger'>Aberta</span>";
            } else {
              alerta = "<span class='badge bg-success'>Erro na cancela</span>";
            }
            break;
          case "ventoinha":
            if (valorNum > 2000) {
              alerta = "<span class='badge bg-danger'>Alto</span>";
            } else if (valorNum < 1000) {
              alerta = "<span class='badge bg-primary'>Baixo</span>";
            } else {
              alerta = "<span class='badge bg-success'>Normal</span>";
            }
            break;
          case "led":
            if (valorNum == "3") {
              alerta = "<span class='badge bg-danger'>Ligado</span>";
            } else if (valorNum == "1") {
              alerta = "<span class='badge bg-success'>Ligado</span>";
            } else if (valorNum == "2") {
              alerta = "<span class='badge bg-warning text-dark'>Ligado</span>";
            } else {
              alerta = "<span class='badge bg-primary'>Desligado</span>";
            }
            break;
          default:
            alerta = "<span class='badge bg-secondary'>Desconhecido</span>";
            break;
        }

        // Adiciona a linha ao histórico

        tr.innerHTML = `
          <td>${valor}</td>
          <td>${dataHora}</td>
          <td>${alerta}</td>
        `;
        tbody.appendChild(tr);
      });
    } catch (erro) {
      console.error("Erro ao carregar o histórico:", erro);
    }
  }

  // Atualiza os sensores a cada 1 segundo
  setInterval(atualizarSensores, 1000);
  // Atualiza o histórico a cada 3 segundos, começando após 1 segundo
  setTimeout(() => setInterval(carregarHistorico, 3000), 1000);
});
